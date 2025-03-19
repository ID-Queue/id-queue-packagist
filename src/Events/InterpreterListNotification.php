<?php

namespace IdQueue\IdQueuePackagist\Events;

use IdQueue\IdQueuePackagist\Enums\UserStatus;
use IdQueue\IdQueuePackagist\Http\Resources\InterpreterResource;
use IdQueue\IdQueuePackagist\Models\Admin\CC2DB;
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
        $status = $this->getUserStatusByGUID($this->updated_user);

        Log::info('Broadcasting event', [
            'event' => $this->message,
            'updated_user' => $this->updated_user,
            'status' => $status,
        ]);

        return [
            'event' => $this->message,
            'data' => [
                'user' => new InterpreterResource($user),
                'status' => $status,
            ],
        ];
    }

    public function getUserStatusByGUID(string $guid)
    {
        $user = User::where('GUID', $guid)
            ->where(function ($query) {
                $query->whereNull('Account_Deleted')
                    ->orWhere('Account_Deleted', false);
            })
            ->first();

        if (! $user) {
            Log::warning('User not found in getUserStatusByGUID', ['guid' => $guid]);
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($user->isStationed == true && $user->Staff_Login_State == 1) {
            Log::info('User is stationed', ['guid' => $guid]);
            return 'stationed';
        }

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
            if ($key === 'lunchandna' && in_array($user->getStatus(), $status)) {
                Log::info('User status identified as lunchandna', ['guid' => $guid]);
                return 'lunchandna';
            }

            if ($user->getStatus() === $status) {
                Log::info("User status identified as {$key}", ['guid' => $guid]);
                return $key;
            }
        }

        Log::warning('User status is unknown', ['guid' => $guid]);
        return 'unknown';
    }
}
