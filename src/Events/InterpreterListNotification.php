<?php

namespace IdQueue\IdQueuePackagist\Events;

use IdQueue\IdQueuePackagist\Enums\UserStatus;
use IdQueue\IdQueuePackagist\Http\Resources\InterpreterResource;
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
use Illuminate\Support\Facades\Log;

class InterpreterListNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $message;
    public string $group;
    public string $companyCode;
    public int $deptID;
    public string $user_id;
    public string $updated_user;

    public function __construct(string $message, string $group, string $companyCode, int $deptID, string $user_id, string $updated_user)
    {
        $this->message = $message;
        $this->group = $group;
        $this->companyCode = $companyCode;
        $this->deptID = $deptID;
        $this->user_id = $user_id;
        $this->updated_user = $updated_user;

        Log::info('InterpreterListNotification Event Initialized', [
            'message' => $message,
            'group' => $group,
            'companyCode' => $companyCode,
            'deptID' => $deptID,
            'user_id' => $user_id,
            'updated_user' => $updated_user,
        ]);

        $db = CC2DB::where('Company_Code', $this->companyCode)->first();

        if (! $db) {
            Log::error('Database configuration not found for Company Code: ' . $this->companyCode);
            throw new ModelNotFoundException("Database configuration not found for Company Code: {$this->companyCode}");
        }

        Log::info('Database connection found, setting up connection.');
        ConnectionService::setConnection($db);
        DB::purge('db_connection');

        $user = User::where('GUID', $user_id)->first();

        if ($user) {
            Log::info('User found', ['user_id' => $user_id]);
            // Auth::loginUsingId($user->id);
        } else {
            Log::warning('User not found', ['user_id' => $user_id]);
        }
    }

    public function broadcastOn(): PrivateChannel
    {
        $uniqueGroupCode = hash('sha256', $this->group . $this->user_id . $this->companyCode . $this->deptID);
        return new PrivateChannel('group.' . $uniqueGroupCode);
    }

    public function broadcastAs(): string
    {
        return 'interpreter.updated';
    }

    public function broadcastWith(): array
    {
        $user = User::where('GUID', $this->updated_user)->first();
        $status = $this->getUserStatusByGUID($user);
       // dd($status,  $user);
        Log::info('Broadcasting event', [
            'event' => $this->message,
            'updated_user' => $this->updated_user,
            'status' => $status,
        ]);

        return [
            'event' => $this->message,
            'data' => [
                'user' => new InterpreterResource($user),
                'status' => UserStatus::getKey($status),
            ],
        ];
    }
    
    public function getUserStatusByGUID(User $user)
    {

        $currentStatus = ActiveQueue::returnStaffCurrentStatus($user->GUID, $user->Company_Dept_ID);
    
        
        if ($currentStatus > 0) {
            return $this->mapStatus($currentStatus, $user);
        }

        if (ActiveQueue::returnIfDispatchedToStaff($user->GUID, $user->Company_Dept_ID)) {
            return UserStatus::Dispatched;
        }
        if ((int) $user->Staff_Login_State === 1) {
            return UserStatus::Available;
        }
        // if ((int) $user->Staff_Login_State === 2) {
        //     return UserStatus::Lunch;
        // }
        // if ((int) $user->Staff_Login_State === 3) {
        //     return UserStatus::NotAvailable;
        // }

        if((int) $user->Staff_Login_State === 2 || (int) $user->Staff_Login_State === 3){
            return "lunchandna";
        }


        if ((int) $user->Staff_Login_State == 0) {
            return UserStatus::CheckOut;
        }

        return UserStatus::LoggedOut;
        
    }
    private function mapStatus(int $currentStatus, User $user): int
    {
     
        $statusMapping = [
            7 => UserStatus::Paused, // Paused
            4 => UserStatus::InProgress, // In session
            3 => UserStatus::Arrived, // SW
            2 => UserStatus::Accepted, // Thumbs
        ];
   

        // Return the mapped status or default to the current staff login state
        return $statusMapping[$currentStatus] ?? $user->Staff_Login_State; // Default to current state if no match
    }
}
