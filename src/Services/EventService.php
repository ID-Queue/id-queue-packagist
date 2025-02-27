<?php

namespace IdQueue\IdQueuePackagist\Services;

use IdQueue\IdQueuePackagist\Events\RequestListNotification;
use IdQueue\IdQueuePackagist\Events\InterpreterListNotification;
use IdQueue\IdQueuePackagist\Enums\EventType;
use IdQueue\IdQueuePackagist\Models\Company\User;
use Illuminate\Support\Facades\Event;

class EventService
{
    protected $users;
    protected string $companyCode;
    protected int $deptID;

    /**
     * Constructor to fetch users dynamically.
     */
    public function __construct(int $departmentId, string $companyCode)
    {
        $this->deptID = $departmentId;
        $this->companyCode = $companyCode;

        // Fetch users based on conditions
        $this->users = User::where('Company_Dept_ID', $departmentId)
            ->where(function ($query) {
                $query->whereNull('Account_Deleted')
                    ->orWhere('Account_Deleted', false);
            })
            ->where(function ($query) {
                $query->where('Staff_Login_State', '1')
                    ->orWhere('loggedInStatus', '1');
            })
            ->get();
    }

    /**
     * Dispatch events dynamically based on event types and parameters.
     *
     * @param array $eventTypes  List of event types (Enums).
     * @param array $extraParams Additional parameters for events.
     */
    public function dispatchEvents(array $eventTypes, array $extraParams = [])
    {
        if(isset($extraParams['checkedInIds'])){
            $this->users = $this->users->whereIn('GUID',$extraParams['checkedInIds'] )->all();
        }
     
        foreach ($this->users as $user) {
            
            foreach ($eventTypes as $eventType) {
                switch ($eventType) {
                    case EventType::INTERPRETER_UPDATED:
                        Event::dispatch(new InterpreterListNotification(
                            'interpreter-updated',
                            'staff',
                            $this->companyCode,
                            $this->deptID,
                            $user->GUID,
                            $extraParams['updated_user'] ?? null  // Dynamically handle request_id
                        ));
                        break;

                    case EventType::DISPATCH_UPDATED:
                        Event::dispatch(new RequestListNotification(
                            'dispatch-updated',
                            'staff',
                            $this->companyCode,
                            $this->deptID,
                            $user->GUID,
                            $extraParams['request_id'] ?? null  // Dynamically handle request_id
                        ));
                        break;
                }
            }
        }
    }
}
