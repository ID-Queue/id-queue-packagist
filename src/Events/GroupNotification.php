<?php

namespace IdQueue\IdQueuePackagist\Events;

use IDQueue\IDQueuePackagist\Enums\RequestStatus;
use IdQueue\IdQueuePackagist\Enums\UserStatus;
use IdQueue\IdQueuePackagist\Http\Resources\DispatchRequestList;
use IdQueue\IdQueuePackagist\Http\Resources\InterPreterResourceList;
use IdQueue\IdQueuePackagist\Models\Admin\CC2DB;
use IdQueue\IdQueuePackagist\Models\Company\ActiveQueue;
use IdQueue\IdQueuePackagist\Models\Company\User;
use IdQueue\IdQueuePackagist\Services\ConnectionService;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Message content.
     */
    public string $message;

    /**
     * Group identifier.
     */
    public string $group;

    /**
     * Company code.
     */
    public string $companyCode;

    /**
     * Department ID.
     */
    public int $deptID;

    public string $user_id;

    /**
     * Initialize a new GroupNotification instance.
     *
     * @param  string  $message  The notification message.
     * @param  string  $group  The group identifier.
     * @param  string  $companyCode  The company code.
     * @param  int  $deptID  The department ID.
     */
    public function __construct(string $message, string $group, string $companyCode, int $deptID, string $user_id)
    {
        $this->message = $message;
        $this->group = $group;
        $this->companyCode = $companyCode;
        $this->deptID = $deptID;
        $this->user_id = $user_id;

        $db = CC2DB::where('Company_Code', $this->companyCode)->first();

        // Throw error if not found
        if (! $db) {
            throw new ModelNotFoundException("Database configuration not found for Company Code: {$this->companyCode}");
        }

        // Set the connection
        ConnectionService::setConnection($db);
        DB::purge('db_connection');
        $user = User::where('GUID', $user_id)->first();
        // Auth::loginUsingId();
        if ($user) {

            Auth::loginUsingId($user->id);

        }
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): PrivateChannel
    {
        $uniqueGroupCode = hash('sha256', $this->group.$this->user_id.$this->companyCode.$this->deptID);

        return new PrivateChannel('group.'.$uniqueGroupCode);
    }

    /**
     * Get the event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'request.updated';
    }

    /**
     * Get the data to broadcast with the event.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'event' => $this->message,
            'group' => $this->group,
            'companyCode' => $this->companyCode,
            'deptID' => $this->deptID,
            'data' => [
                'dispatch' => [
                    'interpreterlist' => json_encode($this->getOnlineInterpreterList()),
                    'dispatchlist' =>  json_encode($this->getDispatchList()),
                ],
            ],
        ];
    }

    public function getOnlineInterpreterList()
    {
        $departmentId = $this->deptID;
        $users = User::where('Company_Dept_ID', $this->deptID)
            ->where('Type_Staff', '1')
            ->where(function ($query) {
                $query->whereNull('Account_Deleted')
                    ->orWhere('Account_Deleted', false);
            })
            ->get();

        $data = (object) [
            'department' => $departmentId,
            'users' => collect([
                'available' => UserStatus::Available,
                'arrived' => UserStatus::Arrived,
                'dispatched' => UserStatus::Dispatched,
                'session' => UserStatus::InProgress,
                'paused' => UserStatus::Paused,
                'stationed' => UserStatus::Stationed,
                'accepted' => UserStatus::Accepted,
                'lunchandna' => [
                    UserStatus::Lunch()->value,
                    UserStatus::NotAvailable()->value,
                ],
            ])->mapWithKeys(function ($status, $key) use ($users) {
                // Filter users based on stationed condition first

                if ($key === 'stationed') {
                    $filteredUsers = $users->filter(fn ($user) => ($user->isStationed == true && $user->Staff_Login_State == 1));

                    return [$key => $filteredUsers];
                }
                $filteredUsers = $users->filter(fn ($user) => ($user->isStationed == false || $user->isStationed == null));
                // If status is 'lunchandna', check for array of statuses
                if ($key === 'lunchandna') {
                    return [$key => $filteredUsers->filter(fn ($user) => in_array($user->getStatus(), $status))];
                }

                // For other statuses, just match the status
                return [$key => $filteredUsers->filter(fn ($user) => $user->getStatus() === $status)];
            }),
        ];

        // Return the prepared data as a resource
        return new InterPreterResourceList($data);
    }

    public function getDispatchList()
    {

        // Mock data (replace these with actual queries from the database)
        // Fetch department ID from the request

        $departmentId = $this->deptID;

        // Fetch data dynamically (replace mock data with actual database queries)
        $data = (object) [
            'department' => $departmentId,
            'queues' => [
                'pending' => ActiveQueue::fetchActiveQueue($departmentId, RequestStatus::App_Pending()),
                'dispatched' => ActiveQueue::fetchActiveQueue($departmentId, RequestStatus::App_Dispatched()),
                'paused' => ActiveQueue::fetchActiveQueue($departmentId, RequestStatus::App_Paused()),
                'accepted' => ActiveQueue::fetchActiveQueue($departmentId, RequestStatus::App_Approved()),
                'arrived' => ActiveQueue::fetchActiveQueue($departmentId, RequestStatus::App_Arrived()),
                'insession' => ActiveQueue::fetchActiveQueue($departmentId, RequestStatus::App_Session()),
            ],
        ];

        // Return the prepared data as a resource
        return new DispatchRequestList($data);

    }
}
