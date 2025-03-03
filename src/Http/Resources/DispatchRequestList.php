<?php

namespace IdQueue\IdQueuePackagist\Http\Resources;

use IdQueue\IdQueuePackagist\Enums\RequestColumn;
use IdQueue\IdQueuePackagist\Models\Company\AdminServiceSetting;
use IdQueue\IdQueuePackagist\Models\Company\DeptPreSetting;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class DispatchRequestList extends JsonResource
{
    public function toArray($request)
    {

        return [

            'requests' => [
                'columns' => DispatchAndInterpreterResource::getColumns($this->resource->department)->toArray(), // Add columns object
                'pending' => $this->mapRequestStatus($this->resource->queues['pending'] ?? []),
                'dispatched' => $this->mapRequestStatus($this->resource->queues['dispatched'] ?? []),
                'paused' => $this->mapRequestStatus($this->resource->queues['paused'] ?? []),
                'accepted' => $this->mapRequestStatus($this->resource->queues['accepted'] ?? []),
                'arrived' => $this->mapRequestStatus($this->resource->queues['arrived'] ?? []),
                'insession' => $this->mapRequestStatus($this->resource->queues['insession'] ?? []),
            ],
        ];

    }

    /**
     * Helper to map request statuses.
     */
    private function mapRequestStatus(Collection $requests): AnonymousResourceCollection
    {
        return RequestResource::collection($requests);
    }

    /**
     * Helper to generate columns for request_status.
     */
    public static function getColumns($departmentId): Collection
    {
        // Fetch necessary settings in one query using `select` and `first` for more efficient retrieval
        $deptSettings = DeptPreSetting::where('Company_Dept_ID', $departmentId)
            ->select('Service_Single', 'Staff_Single', 'Location_Single', 'Zone_Single', 'Building_Single', 'Person_ID', 'Second_Person_ID', 'Requester_ID')
            ->first();

        // Fetch the admin settings
        $adminSettings = AdminServiceSetting::where('Company_Dept_ID', $departmentId)
            ->select('Enable_Pre_Schedual', 'Enable_Second_Person_ID', 'App_Location_Show', 'App_Location_Detail_Show', 'App_Zone_Show')
            ->first();

        // Initialize the columns array with common fields and conditionally append columns
        $columns = [
            //            ['id' => RequestColumn::STATUS, 'label' => 'Status', 'sortable' => true],
            //            ['id' => RequestColumn::DATE, 'label' => 'Date', 'sortable' => true],
            ['id' => RequestColumn::PERSON_ID, 'label' => $deptSettings->Person_ID, 'sortable' => true],
            $adminSettings->Enable_Second_Person_ID ? ['id' => RequestColumn::SECOND_PERSON_ID, 'label' => $deptSettings->Second_Person_ID, 'sortable' => true] : null,
            ['id' => RequestColumn::BUILDING, 'label' => $deptSettings->Building_Single, 'sortable' => true],
            $adminSettings->App_Zone_Show ? ['id' => RequestColumn::ZONE, 'label' => $deptSettings->Zone_Single, 'sortable' => true] : null,
            $adminSettings->App_Location_Show ? ['id' => RequestColumn::LOCATION, 'label' => $deptSettings->Location_Single, 'sortable' => true] : null,
            $adminSettings->App_Location_Detail_Show ? ['id' => RequestColumn::LOCATION_DETAIL, 'label' => 'Location Detail', 'sortable' => true] : null,
            ['id' => RequestColumn::SERVICE, 'label' => $deptSettings->Service_Single, 'sortable' => true],
            ['id' => RequestColumn::VISIT_TYPE, 'label' => 'Visit Type', 'sortable' => true],
            ['id' => RequestColumn::ASSIGN_STAFF, 'label' => 'Assign/Reassign <br>'.$deptSettings->Staff_Single, 'sortable' => true],
            ['id' => RequestColumn::REQUESTER_ID, 'label' => $deptSettings->Requester_ID, 'sortable' => true],
            ['id' => RequestColumn::EXT, 'label' => 'Ext', 'sortable' => true],
            ['id' => RequestColumn::REQUESTED_TIME, 'label' => 'Requested Time', 'sortable' => true],
            //            in_array($adminSettings->Enable_Pre_Schedual, [1, 2]) ? ['id' => RequestColumn::APPOINTMENT_DATE, 'label' => 'Appointment Date', 'sortable' => true] : null,
        ];

        // Remove null values from the array (this happens when a condition is not met)
        return collect(array_filter($columns, fn ($column) => $column !== null));
    }
}
