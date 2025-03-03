<?php

namespace IdQueue\IdQueuePackagist\Events;

use IDQueue\IDQueuePackagist\Enums\RequestStatus;
use IdQueue\IdQueuePackagist\Enums\UserStatus;
use IdQueue\IdQueuePackagist\Http\Resources\DispatchRequestList;
use IdQueue\IdQueuePackagist\Http\Resources\InterPreterResourceList;
use IdQueue\IdQueuePackagist\Http\Resources\RequestResource;
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

class RequestListNotification implements ShouldBroadcastNow
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

    public string $request_id;

    /**
     * Initialize a new GroupNotification instance.
     *
     * @param  string  $message  The notification message.
     * @param  string  $group  The group identifier.
     * @param  string  $companyCode  The company code.
     * @param  int  $deptID  The department ID.
     */
    public function __construct(string $message, string $group, string $companyCode, int $deptID, string $user_id, string $request_id = null)
    {
        $this->message = $message;
        $this->group = $group;
        $this->companyCode = $companyCode;
        $this->deptID = $deptID;
        $this->user_id = $user_id;
        $this->request_id = $request_id;

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
        return 'dispatch-updated';
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
            'data' => [
                "request" => new RequestResource(ActiveQueue::where('GUID', $this->request_id)->first()),
                "status" => ActiveQueue::getStatusByGUID($this->request_id)
            ]
           
        ];
    }

}
