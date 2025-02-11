<?php

namespace IdQueue\IdQueuePackagist\Http\Resources;

use Carbon\Carbon;
use IdQueue\IdQueuePackagist\Enums\RequestColumn;
use IdQueue\IdQueuePackagist\Enums\RequestPriority;
use IdQueue\IdQueuePackagist\Models\Company\ActiveQueue;
use IdQueue\IdQueuePackagist\Models\Company\AdminServiceSetting;
use IdQueue\IdQueuePackagist\Models\Company\DeleteReason;
use IdQueue\IdQueuePackagist\Models\Company\DispatchChartDetails;
use IdQueue\IdQueuePackagist\Models\Company\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class RequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {

        // Retrieve columns for the department and pluck the 'id' field
        $columns = DispatchAndInterpreterResource::getColumns($this->resource->Company_Dept_ID)->pluck('id');

        // Initialize an array to store the transformed response
        $responseData = [];
        $responseData['guid'] = $this->GUID;
        $record = ActiveQueue::where('GUID', $this->GUID)->first();

        // Iterate over each column to match with the predefined labels/values dynamically
        foreach ($columns as $column) {
            switch ($column) {
                //                case RequestColumn::STATUS:
                //                    // Add the corresponding value for the 'Status' column
                //                    $responseData[$column] = $this->getStatusValue(); // Use an appropriate method to fetch the status value
                //                    break;

                case RequestColumn::DATE:
                    // Add the corresponding value for the 'Date' column
                    $responseData[$column] = $this->getDateValue(); // Use an appropriate method to fetch the date value
                    break;

                case RequestColumn::PERSON_ID:
                    // Add the corresponding value for 'Person ID'
                    $responseData[$column] = $this->getPersonIdValue(); // Method for fetching Person ID
                    break;

                case RequestColumn::SECOND_PERSON_ID:
                    // Add the corresponding value for 'Second Person ID'
                    $responseData[$column] = $this->getSecondPersonIdValue(); // Method for fetching Second Person ID
                    break;

                case RequestColumn::BUILDING:
                    // Add the corresponding value for 'Building'
                    $responseData[$column] = $this->getBuildingValue(); // Method for fetching Building
                    break;

                case RequestColumn::ZONE:
                    // Add the corresponding value for 'Zone'
                    $responseData[$column] = $this->getZoneValue(); // Method for fetching Zone
                    break;

                case RequestColumn::LOCATION:
                    // Add the corresponding value for 'Location'
                    $responseData[$column] = $this->getLocationValue(); // Method for fetching Location
                    break;

                case RequestColumn::LOCATION_DETAIL:
                    // Add the corresponding value for 'Location Detail'
                    $responseData[$column] = $this->getLocationDetailValue(); // Method for fetching Location Detail
                    break;

                case RequestColumn::SERVICE:
                    // Add the corresponding value for 'Service'
                    $responseData[$column] = $this->getServiceValue(); // Method for fetching Service
                    break;

                case RequestColumn::VISIT_TYPE:
                    // Add the corresponding value for 'Visit Type'
                    $responseData[$column] = $this->getVisitTypeValue(); // Method for fetching Visit Type
                    break;

                case RequestColumn::ASSIGN_STAFF:
                    // Add the corresponding value for 'Assign/Reassign Staff'
                    $responseData[$column] = $this->getAssignStaffValue(); // Method for fetching Assign Staff
                    break;

                case RequestColumn::REQUESTER_ID:
                    // Add the corresponding value for 'Requester ID'
                    $responseData[$column] = $this->getRequesterIdValue(); // Method for fetching Requester ID
                    break;

                case RequestColumn::EXT:
                    // Add the corresponding value for 'Ext'
                    $responseData[$column] = $this->getExtValue(); // Method for fetching Ext
                    break;

                case RequestColumn::REQUESTED_TIME:
                    // Add the corresponding value for 'Requested Time'
                    $responseData[$column] = $this->getRequestedTimeValue(); // Method for fetching Requested Time
                    break;

                case RequestColumn::APPOINTMENT_DATE:
                    // Add the corresponding value for 'Appointment Date'
                    $responseData[$column] = $this->getAppointmentDateValue(); // Method for fetching Appointment Date
                    break;

                default:
                    // Handle unexpected cases or columns if needed (optional)
                    // $responseData[$column] = 'N/A'; // For example, you can set a default value for unknown columns
                    break;
            }
        }
        $lifeline = DispatchChartDetails::where('Request_ID', $record->ID)->get();
        $responseData['lifelines'] = RequestLifeLineResource::collection($lifeline);
        $responseData['dispatch_notes'] = $this->Dispatch_Notes;
        $responseData['final_notes'] = $this->Final_Notes;
        $responseData['notes'] = $this->Notes;
        $responseData['release_notes'] = $this->Release_Notes;
        $responseData['priority'] = (int) $this->Priority;
        $responseData['bg_colour'] = RequestPriority::color((int) $this->Priority);
        $responseData['staff_status'] = $this->getStaffStatus();
        $responseData['id'] = $record->ID;
        if ($request->input('cancelreasons')) {
            $responseData['cancelreasons'] = DeleteReason::where([
                'Company_Dept_ID' => $request->input('d_id'),
            ])->orderBy('name')->get();
        }

        // Return the transformed response data
        return $responseData;
    }

    //    public function getStatusValue()
    //    {
    //        // Replace with the logic to fetch the actual status value
    //        // Example: Access a status column from a database record
    //        return $this->status ?? 'N/A'; // Default to 'N/A' if status is not set
    //    }

    public function getStaffStatus(): ?string
    {

        if ((bool) $this->resource->App_Approved && $this->resource->Staff_GUID) {
            return 'Dispatched';
        }

        return $this->resource->Staff_GUID ? 'Applied' : null;
    }

    public function getDateValue(): string
    {
        // Replace with logic to fetch and format the date
        // Example: Access date and format it if necessary
        return $this->Req_Time ? Carbon::parse($this->Req_Time)->format('Y-m-d H:i') : 'N/A'; // Default to 'N/A' if no date
    }

    public function getPersonIdValue()
    {
        // Replace with logic to fetch the person ID value
        return $this->Pat_MRN ?? 'N/A'; // Default to 'N/A' if person ID is not set
    }

    public function getSecondPersonIdValue()
    {
        // Replace with logic to fetch the second person ID value
        return $this->Pat_Sec_ID ?? 'N/A'; // Default to 'N/A' if second person ID is not set
    }

    public function getBuildingValue()
    {
        // Replace with logic to fetch building value
        return $this->dispatchBuilding ?? 'N/A'; // Default to 'N/A' if building is not set
    }

    public function getZoneValue()
    {
        // Replace with logic to fetch zone value
        return $this->dispatchZone ?? 'N/A'; // Default to 'N/A' if zone is not set
    }

    public function getLocationValue()
    {
        // Replace with logic to fetch location value
        return $this->dispatchLocation ?? 'N/A'; // Default to 'N/A' if location is not set
    }

    public function getLocationDetailValue()
    {
        // Replace with logic to fetch location detail value
        return $this->App_LocDetail ?? 'N/A'; // Default to 'N/A' if location detail is not set
    }

    public function getServiceValue()
    {
        // Replace with logic to fetch the service value
        return $this->App_Service ?? 'N/A'; // Default to 'N/A' if service is not set
    }

    public function getVisitTypeValue()
    {
        // Replace with logic to fetch visit type value
        return $this->App_Visit_Type ?? 'N/A'; // Default to 'N/A' if visit type is not set
    }

    public function getAssignStaffValue()
    {
        // Replace with logic to fetch assign staff value
        return new StaffResource(User::where('GUID', $this->Staff_GUID)->first()) ?? (object) []; // Default to 'N/A' if assign staff is not set
    }

    public function getRequesterIdValue()
    {
        // Replace with logic to fetch requester ID value
        return $this->Who_Is_Name ?? 'N/A'; // Default to 'N/A' if requester ID is not set
    }

    public function getExtValue()
    {
        // Replace with logic to fetch ext value
        return $this->Who_Is_Ext ?? 'N/A'; // Default to 'N/A' if ext is not set
    }

    public function getRequestedTimeValue(): string
    {
        // Enable_Pre_Schedual
        if (AdminServiceSetting::getSettingFor('Enable_Pre_Schedual') && $this->resource->App_Pre_Schedual_Time != null) {
            return $this->Pre_Req_Time
                ? Carbon::parse($this->Pre_Req_Time)->format('m/d/Y h:i A') // Includes date and time
                : 'N/A'; // Default to 'N/A' if no requested time
        }

        return $this->Req_Time
            ? Carbon::parse($this->Req_Time)->format('m/d/Y h:i A') // Includes date and time
            : 'N/A'; // Default to 'N/A' if no requested time
    }

    public function getAppointmentDateValue(): string
    {
        if (AdminServiceSetting::getSettingFor('Enable_Pre_Schedual') && $this->resource->App_Pre_Schedual_Time != null) {
            return $this->App_Pre_Schedual_Time ? Carbon::parse($this->App_Pre_Schedual_Time)->format('Y-m-d H:i') : 'N/A'; // Default to 'N/A' if no appointment date

        }

        return $this->Req_Time ? Carbon::parse($this->Req_Time)->format('Y-m-d') : 'N/A'; // Default to 'N/A' if no appointment date

        // Replace with logic to fetch appointment date value
    }
}
