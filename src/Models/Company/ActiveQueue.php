<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use Auth;
use Carbon\Carbon;
use IdQueue\IdQueuePackagist\Enums\RequestStatus;
use IdQueue\IdQueuePackagist\Enums\UserStatus;
use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use IdQueue\IdQueuePackagist\Utils\Helper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class ActiveQueue extends Model
{
    use CompanyDbConnection;

    public $timestamps = false;  // Explicitly set this to false

    // Specify the table name since it doesn't follow the default plural naming convention

    protected $table = 'Dispatch_Chart_Active_Queue';

    // Set the primary key if it's not the default 'id'

    // Set the key type to string (UUID is typically a string in Laravel)
    protected $primaryKey = 'GUID';

    // Disable automatic timestamp management if your table doesn't have 'created_at' and 'updated_at'
    protected $keyType = 'string';

    // Set the columns that are mass assignable
    protected $fillable = [
        'ID',
        'GUID',
        'Reoccuring_GUID',
        'Company_Dept_ID',
        'App_Time',
        'App_Service',
        'App_Service_GUID',
        'App_Location_GUID',
        'App_Pre_Schedual_Time',
        'App_Zone_GUID',
        'App_Building_GUID',
        'Staff_GUID',
        'Req_Time',
        'App_Visit_Type',
        'App_Visit_Type_ID',
        'Priority',
        'App_Paused',
        'Pat_MRN',
        'Paused_Time',
        'App_Approved',
        'Final_Notes',
        'Approved_Time',
        'App_Session',
        'Session_Time',
        'Pat_ID_FN',
        'Pat_Sec_ID',
        'Who_Is_Name',
        'Pat_ID_LN',
        'App_Arrived',
        'Arrived_Time',
        'App_Done',
        'Done_Time',
        'App_Declined',
        'Declined_Time',
        'Dispatched_Time',
        'Deleted_Reason',
        'Notes',
        'App_LocDetail',
        'Gender_Pref',
        'Who_Is_Ext',
        'Req_DoximityNo',
        'Release_Notes',
        'Req_Video_Conf',
        'Deleted_By_Name',
        'Req_EMail',
        'Pre_Req_Time',
        'Req_Time_Close',
        'Req_Time_Start',
        'Dispatcher_GUID',
        'Dispatch_Notes',
    ];

    /**
     * Set the Dispatch_Notes attribute.
     */
    protected function dispatchNotes(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(set: function (string $value) {
            // Add your custom logic here
            return ['Dispatch_Notes' => trim($value)];
        });
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(DispatchDepartment::class, 'Company_Dept_ID', 'ID');
    }

    // Define relationships to other models
    public function dispatchLocation(): BelongsTo
    {
        return $this->belongsTo(DispatchLocation::class, 'App_Location_GUID');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(DispatchService::class, 'App_Service', 'Service_Name');
    }

    public function dispatchBuilding(): BelongsTo
    {
        return $this->belongsTo(DispatchBuilding::class, 'App_Building_GUID');
    }

    public function dispatchZone(): BelongsTo
    {
        return $this->belongsTo(DispatchZone::class, 'App_Zone_GUID');
    }

    public function appService(): BelongsTo
    {
        return $this->belongsTo(DispatchService::class, 'App_Service_GUID');
    }

    //    public function dispatchService(): HasMany
    //    {
    //        return $this->hasMany(DispatchService::class, 'Service_Name');
    //    }

    public function userAccount(): BelongsTo
    {
        return $this->belongsTo(User::class, 'Staff_GUID', 'GUID');
    }

    public static function returnStaffCurrentStatus($staffID, $dept_ID): int
    {
        $status = self::with('service')
            ->select(
                'Dispatch_Chart_Active_Queue.*',
                DB::raw('
            CASE
                WHEN App_Paused = 1 THEN 7
                WHEN App_Session = 1 THEN 4
                WHEN App_Arrived = 1 THEN 3
                WHEN App_Approved = 1 THEN 2
                ELSE 0
            END as status
        ')
            )
            ->where('Company_Dept_ID', $dept_ID)
            ->where('Staff_GUID', $staffID)
            ->where(function ($query) {
                $query->whereNull('App_Declined')
                    ->orWhere('App_Declined', '<>', 'true');
            })
            ->where(function ($query) {
                $query->whereNull('App_Done')
                    ->orWhere('App_Done', '<>', 'true');
            })
            ->orderByRaw('
        CASE
            WHEN App_Paused = 1 THEN CONVERT(DATETIME, Paused_Time, 101)
            WHEN App_Session = 1 THEN CONVERT(DATETIME, Session_Time, 101)
            WHEN App_Arrived = 1 THEN CONVERT(DATETIME, Arrived_Time, 101)
            WHEN App_Approved = 1 THEN CONVERT(DATETIME, Approved_Time, 101)
            ELSE CONVERT(DATETIME, Req_time, 101)
        END DESC
    ')
            ->first();

        return $status ? $status->status : 0;
    }

    public static function returnIfDispatchedToStaff($staffGUID, $dept_ID): bool
    {
        // Use Laravel Query Builder to count dispatched items
        $count = self::join('Dispatch_Service as ds', 'Dispatch_Chart_Active_Queue.App_Service', '=', 'ds.Service_Name')
            ->where('Dispatch_Chart_Active_Queue.Company_Dept_ID', '=', $dept_ID)
            ->where(function ($query) {
                $query->whereNull('App_Declined')
                    ->orWhere('App_Declined', '<>', 'true');
            })
            ->where(function ($query) {
                $query->whereNull('App_Done')
                    ->orWhere('App_Done', '<>', 'true');
            })
            ->where('Dispatch_Chart_Active_Queue.Staff_GUID', '=', $staffGUID)
            ->whereNull('App_Pre_Schedual_Time')
            ->count();

        // Return true if there are dispatched items, otherwise false
        return $count > 0;
    }

    /**
     * Scope: Add dynamic service filters to the query.
     *
     * Applies filtering for specific services based on the user's admin type
     * and associated service list.
     *
     * @param  Builder  $query  The query builder instance.
     * @param  array  $userServices  List of services to filter by.
     * @return Builder The modified query builder instance.
     */
    public function scopeFilterByUserServices(Builder $query, array $userServices): Builder
    {

        // Add filtering only if the user is not an 'Admin' and the service list is not empty
        if (Auth::user()->Type_Of_Admin !== 'Admin' && $userServices !== []) {
            $query->where(function ($subQuery) use ($userServices) {
                foreach ($userServices as $service) {
                    $subQuery->orWhere('App_Service', $service);
                }
            });
        }

        return $query;
    }

    public static function getMatchingStaffGUIDs($dept_ID, $tmpServ, $tmpLocGUID, $tmpZoneGUID, $tmpBuildGUID): array
    {
        $results = StaffStation::select(
            'Staff_Station.Staff_GUID as staff_guid',
            'Staff_Station.App_Location_GUID as location',
            'Staff_Station.App_Zone_GUID as zone',
            'Staff_Station.App_Building_GUID as building',
            'Dispatch_Staff.Service as service'
        )
            ->join('Dispatch_Staff', 'Dispatch_Staff.Acc_GUID', '=', 'Staff_Station.Staff_GUID')
            ->where('Dispatch_Staff.Company_Dept_ID', $dept_ID)
            ->where('Staff_Station.Company_Dept_ID', $dept_ID)
            ->where('Staff_Station.stationed_status', 1)
            ->get();

        if ($results->isEmpty()) {
            return [];
        }

        $matchingStaff = [];

        foreach ($results as $row) {
            $locationMatch = $row->location == $tmpLocGUID || is_null($row->location);
            $zoneMatch = $row->zone == $tmpZoneGUID || is_null($row->zone);
            $buildingMatch = $row->building == $tmpBuildGUID;
            $serviceMatch = $row->service == $tmpServ;

            if ($buildingMatch && $serviceMatch) {
                if (is_null($tmpZoneGUID) && is_null($tmpLocGUID)) {
                    if (is_null($row->location) && is_null($row->zone)) {
                        $matchingStaff[] = $row->staff_guid;
                    } elseif (is_null($row->location) && $zoneMatch) {
                        $matchingStaff[] = $row->staff_guid;
                    } elseif ($locationMatch && $zoneMatch) {
                        $matchingStaff[] = $row->staff_guid;
                    }
                } elseif (!is_null($tmpZoneGUID) && is_null($tmpLocGUID)) {
                    if (is_null($row->location) && is_null($row->zone)) {
                        $matchingStaff[] = $row->staff_guid;
                    } elseif (is_null($row->location) && $zoneMatch) {
                        $matchingStaff[] = $row->staff_guid;
                    } elseif ($locationMatch && $zoneMatch) {
                        $matchingStaff[] = $row->staff_guid;
                    }
                } elseif (!is_null($tmpZoneGUID) && !is_null($tmpLocGUID)) {
                    if (is_null($row->location) && is_null($row->zone)) {
                        $matchingStaff[] = $row->staff_guid;
                    } elseif (is_null($row->location) && $zoneMatch) {
                        $matchingStaff[] = $row->staff_guid;
                    } elseif ($locationMatch && $zoneMatch) {
                        $matchingStaff[] = $row->staff_guid;
                    }
                }
            }
        }

        return $matchingStaff;
    }


    public static function filterStationed($records): Collection
    {

        $filteredRecords = collect(); // Initialize an empty collection
        $stationedIDs = User::getUsersByStatus(UserStatus::Stationed())->pluck('GUID');

        foreach ($records as $record) {
            self::checkIfServiceInStationedLoc($record->Company_Dept_ID, $record->App_Service, $record->App_Location_GUID, $record->App_Zone_GUID, $record->App_Building_GUID) ? null : $filteredRecords->push($record);
        }

        // Ensure the result is a collection
        return new Collection($filteredRecords->all());
    }

    public static function checkIfServiceInStationedLoc($dept_ID, $tmpServ, $tmpLocGUID, $tmpZoneGUID, $tmpBuildGUID): bool
    {
        $results = StaffStation::select(
            'Staff_Station.App_Location_GUID as location',
            'Staff_Station.App_Zone_GUID as zone',
            'Staff_Station.App_Building_GUID as building',
            'Dispatch_Staff.Service as service'
        )
            ->join('Dispatch_Staff', 'Dispatch_Staff.Acc_GUID', '=', 'Staff_Station.Staff_GUID')
            ->where('Dispatch_Staff.Company_Dept_ID', $dept_ID)
            ->where('Staff_Station.Company_Dept_ID', $dept_ID)
            ->where('Staff_Station.stationed_status', 1)
            ->get();

        if ($results->isEmpty()) {
            return false;
        }

        foreach ($results as $row) {
            $locationMatch = $row->location == $tmpLocGUID || is_null($row->location);
            $zoneMatch = $row->zone == $tmpZoneGUID || is_null($row->zone);
            $buildingMatch = $row->building == $tmpBuildGUID;
            $serviceMatch = $row->service == $tmpServ;

            if ($buildingMatch && $serviceMatch) {
                if (is_null($tmpZoneGUID) && is_null($tmpLocGUID)) {
                    if (is_null($row->location) && is_null($row->zone)) {
                        return true;
                    }
                    if (is_null($row->location) && $zoneMatch) {
                        return true;
                    }
                    if ($locationMatch && $zoneMatch) {
                        return true;
                    }
                } elseif (! is_null($tmpZoneGUID) && is_null($tmpLocGUID)) {
                    if (is_null($row->location) && is_null($row->zone)) {
                        return true;
                    }
                    if (is_null($row->location) && $zoneMatch) {
                        return true;
                    }
                    if ($locationMatch && $zoneMatch) {
                        return true;
                    }
                } elseif (! is_null($tmpZoneGUID) && ! is_null($tmpLocGUID)) {
                    if (is_null($row->location) && is_null($row->zone)) {
                        return true;
                    }
                    if (is_null($row->location) && $zoneMatch) {
                        return true;
                    }
                    if ($locationMatch && $zoneMatch) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Fetch active queue records with dynamic conditions.
     *
     * Retrieve records for a specific department, filtered by the user's associated
     * services, and excludes completed or declined appointments.
     *
     * @param  int  $departmentId  The ID of the company department.
     * @return Collection The resulting collection of records.
     */
    public static function fetchActiveQueue(int $departmentId, RequestStatus $status, bool $value = true, bool $filterStationed = true, bool $websocket = false): Collection
    {
        // Calculate the pre-scheduled time threshold for the department using Carbon
        $preScheduledTimeThreshold = Helper::return_TotalPreschedTime($departmentId);
        $preScheduledTime = Carbon::now()->addMinutes($preScheduledTimeThreshold);

        // Retrieve the list of services associated with the current user
        $userServices = [];
        if (! $websocket) {
            $userServices = Auth::user()->services()->pluck('Service')->toArray();
        }

        // Base query to avoid duplication
        $query = self::query()
            ->join('Dispatch_Service as ds', 'Dispatch_Chart_Active_Queue.App_Service', '=', 'ds.Service_Name') // Join with Dispatch_Service table
            ->where('Dispatch_Chart_Active_Queue.Company_Dept_ID', $departmentId) // Specify the table explicitly
            ->filterByUserServices($userServices) // Apply service-based filters
            ->where(function ($query) {
                $query->whereNull('Dispatch_Chart_Active_Queue.App_Done')
                    ->orWhere('Dispatch_Chart_Active_Queue.App_Done', 0);
            })
            ->where(function ($query) {
                $query->whereNull('Dispatch_Chart_Active_Queue.App_Declined')
                    ->orWhere('Dispatch_Chart_Active_Queue.App_Declined', 0);
            });

        if ($status->value !== RequestStatus::App_Paused) {
            $query = $query->where(function ($query) {
                $query->where('Dispatch_Chart_Active_Queue.App_Paused', false)
                    ->orWhereNull('Dispatch_Chart_Active_Queue.App_Paused');
            });
        }
        if ($status->value === RequestStatus::App_Dispatched) {
            $query = $query->where(function ($query) {
                $query->where('Dispatch_Chart_Active_Queue.App_Approved', false)
                    ->orWhereNull('Dispatch_Chart_Active_Queue.App_Approved');
            });
        }
        if ($status->value === RequestStatus::App_Approved) {
            $query = $query->where(function ($query) {
                $query->where('Dispatch_Chart_Active_Queue.App_Arrived', false)
                    ->orWhereNull('Dispatch_Chart_Active_Queue.App_Arrived');
            });
        }
        if ($status->value === RequestStatus::App_Arrived) {
            $query = $query->where(function ($query) {
                $query->where('Dispatch_Chart_Active_Queue.App_Session', false)
                    ->orWhereNull('Dispatch_Chart_Active_Queue.App_Session');
            });
        }
        $query = $query->where(function ($query) use ($preScheduledTime) {
            // Include records with pre-schedule times <= the threshold or NULL
            $query->where('Dispatch_Chart_Active_Queue.App_Pre_Schedual_Time', '<=', $preScheduledTime)
                ->orWhereNull('Dispatch_Chart_Active_Queue.App_Pre_Schedual_Time');
        })
            ->orderBy('Dispatch_Chart_Active_Queue.Priority', 'ASC') // Specify the table explicitly
            ->orderByRaw('CONVERT(datetime, Dispatch_Chart_Active_Queue.Req_time, 101) ASC'); // Specify the table explicitly

        // Apply the status condition based on whether it's pending or another status

        if ($status->value === RequestStatus::App_Pending) {
            $records = $query->where([
                RequestStatus::App_Approved => null,
                RequestStatus::App_Arrived => null,
                RequestStatus::App_Dispatched => null,
                RequestStatus::App_Session => null,
                RequestStatus::App_Paused => null,
            ])
                ->get(); // Return query result for pending status
            if ($filterStationed) {
                return self::filterStationed($records)->unique('GUID');
            }

            return $records->unique('GUID');

        }

        // For other statuses, apply the status value filter
        $records = $query->where($status->value, $value)
            ->get(); // Return query result for other statuses

        if ($filterStationed) {
            return self::filterStationed($records)->unique('GUID');
        }

        return $records->unique('GUID');
    }

    public static function getStatusByGUID(string $guid): ?RequestStatus
{
        // Retrieve the record with the given GUID
        $record = self::query()
            ->where('GUID', $guid)
            ->first();

        // If no record is found, return null
        if (! $record) {
            return null;
        }

        // Check the status based on the available fields
        if ($record->App_Session) {
            return RequestStatus::App_Session();
        }
        if ($record->App_Arrived) {
            return RequestStatus::App_Arrived();
        }
        if ($record->App_Approved) {
            return RequestStatus::App_Approved();
        }
        if ($record->App_Dispatched) {
            return RequestStatus::App_Dispatched();
        }
        if ($record->App_Paused) {
            return RequestStatus::App_Paused();
        }
        if (is_null($record->App_Approved) && is_null($record->App_Arrived) &&
            is_null($record->App_Dispatched) && is_null($record->App_Session) &&
            is_null($record->App_Paused)) {
            return RequestStatus::App_Pending();
        }

        return null; // If no specific status is found
}


    public function lifeline(): HasMany
    {
        return $this->hasMany(DispatchChartDetails::class, 'Request_ID', 'ID');
    }
}
