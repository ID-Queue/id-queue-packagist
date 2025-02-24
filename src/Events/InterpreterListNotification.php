<?php

namespace IdQueue\IdQueuePackagist\Events;

use IDQueue\IDQueuePackagist\Enums\RequestStatus;
use IdQueue\IdQueuePackagist\Enums\UserStatus;
use IdQueue\IdQueuePackagist\Http\Resources\DispatchRequestList;
use IdQueue\IdQueuePackagist\Http\Resources\InterpreterResource;
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

class InterpreterListNotification implements ShouldBroadcastNow
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
    public string $updated_user;

    /**
     * Initialize a new GroupNotification instance.
     *
     * @param  string  $message  The notification message.
     * @param  string  $group  The group identifier.
     * @param  string  $companyCode  The company code.
     * @param  int  $deptID  The department ID.
     */
    public function __construct(string $message, string $group, string $companyCode, int $deptID, string $user_id, string $updated_user)
    {
        $this->message = $message;
        $this->group = $group;
        $this->companyCode = $companyCode;
        $this->deptID = $deptID;
        $this->user_id = $user_id;
        $this->updated_user = $updated_user;

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
        return 'interpreter.updated';
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
                "user" => new InterpreterResource(User::where('GUID', $this->updated_user)->first()),
                "status" => $this->getUserStatusByGUID( $this->updated_user)
            ],
        ];
    }

    public function getUserStatusByGUID(string $guid)
    {
        // Find user by GUID
        $user = User::where('GUID', $guid)
            ->where(function ($query) {
                $query->whereNull('Account_Deleted')
                    ->orWhere('Account_Deleted', false);
            })
            ->first();
    
        // If user not found, return null or a response
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        // Check if user is stationed
        if ($user->isStationed == true && $user->Staff_Login_State == 1) {
            return 'stationed';
        }
    
        // Match user status dynamically
        $statuses = [
            'available' => UserStatus::Available,
            'arrived' => UserStatus::Arrived,
            'dispatched' => UserStatus::Dispatched,
            'session' => UserStatus::InProgress,
            'paused' => UserStatus::Paused,
            'accepted' => UserStatus::Accepted,
            'lunchandna' => [
                UserStatus::Lunch()->value,
                UserStatus::NotAvailable()->value,
            ],
        ];
    
        foreach ($statuses as $key => $status) {
            // Check for lunchandna separately
            if ($key === 'lunchandna' && in_array($user->getStatus(), $status)) {
                return 'lunchandna';
            }
    
            // Match the exact status
            if ($user->getStatus() === $status) {
                return $key;
            }
        }
    
        // Default fallback if no status matches
        return 'unknown';
    }
    
}
