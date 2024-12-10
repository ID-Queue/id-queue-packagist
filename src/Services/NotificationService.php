<?php

namespace IdQueue\IdQueuePackagist\Services;

use IdQueue\IdQueuePackagist\Models\Company\DeptPreSetting;
use IdQueue\IdQueuePackagist\Models\Company\DispatchChart;
use IdQueue\IdQueuePackagist\Models\Company\User;
use IdQueue\IdQueuePackagist\Utils\Helper;
use Log;
use Throwable;

class NotificationService
{
    private MailService $mailService;

    /**
     * Constructor to inject MailService
     */
    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * Notify staff about a request.
     *
     * @throws Throwable
     */
    public function notifyStaff(int $deptId, string $serviceName, ?string $bcc, string $url): void
    {
        // Retrieve emails of staff members who match the criteria
        $emails = User::query()
            ->join('Dispatch_Staff as ds', 'ds.Acc_GUID', '=', 'User_Accounts.GUID')
            ->where('User_Accounts.Company_Dept_ID', $deptId)
            ->where('ds.Company_Dept_ID', $deptId)
            ->where('ds.Service', $serviceName)
            ->whereNotNull('User_Accounts.email')
            ->where('User_Accounts.email_when_req', 1)
            ->where(function ($query) {
                $query->where('User_Accounts.Account_Deleted', 0)
                    ->orWhereNull('User_Accounts.Account_Deleted');
            })
            ->pluck('User_Accounts.email')
            ->toArray(); // Ensures it's an array

        // Ensure there are emails to send
        if (empty($emails)) {
            // Log or handle the case where no emails are found
            Log::info("No emails found for notifying staff in dept {$deptId} for service {$serviceName}.");

            return;
        }

        // Prepare email data
        $emailData = [
            'emails' => $emails,
            'bcc' => $bcc ? explode(',', $bcc) : [], // Handle optional BCC if provided
            'type' => 'Submit',
            'data' => [
                'url' => $url, // Dynamic data to include in the email
            ],
        ];

        try {
            // Call the mail service to send the email
            $this->mailService->staffEmailRequest($emailData);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Failed to notify staff: '.$e->getMessage(), [
                'deptId' => $deptId,
                'serviceName' => $serviceName,
                'bcc' => $bcc,
                'url' => $url,
            ]);
        }
    }

    /**
     * Notify requester about submission success.
     */
    public function notifyRequester(int $deptId, int $idVal, string $subject): array
    {
        $dispatchDataArray = $this->getDispatchChartData($deptId, $idVal);

        if (empty($dispatchDataArray) || ! isset($dispatchDataArray[0])) {
            return [
                'status' => 'error',
                'message' => 'No dispatch data found for the given ID and department.',
            ];
        }

        $dispatchData = $dispatchDataArray[0];

        // Get department values
        $deptSettings = DeptPreSetting::where('Company_Dept_ID', $deptId)
            ->select('Service_Single', 'Staff_Single', 'Location_Single', 'Building_Single', 'Person_ID')
            ->first();
        $emailRequestData = [
            'to' => $dispatchData['Req_EMail'] ?? 'default-email@example.com',
            'subject' => $subject,
            'msg' => 'This is a follow-up message.',
            'details' => [
                'hdr' => "ID-Queue Info: Request for #$idVal",
                'bdy' => 'We are pleased to inform you that your request was successfully submitted.',
                'tmpID' => $dispatchData['ID'],
                'tmpReqTime' => $dispatchData['Req_Time'] ?? 'N/A',
                'tmpBld' => $dispatchData['Building_Name'] ?? $deptSettings->Building_Single,
                'tmpLoc' => $dispatchData['Location_Name'] ?? $deptSettings->Location_Single,
                'locationSingle' => $deptSettings->Location_Single,
                'personID' => $deptSettings->Person_ID,
                'tmpPatMRN' => $dispatchData['Pat_MRN'] ?? 'N/A',
                'tmpVt' => $dispatchData['Visit_Type'] ?? 'N/A',
                'tmpJob' => $dispatchData['Job'] ?? $deptSettings->Service_Single,
                'serviceSingle' => $deptSettings->Service_Single,
                'staffSingle' => $deptSettings->Staff_Single,
                'tmpApprBy' => $dispatchData['Approved_By'] ?: 'N/A',
                'tmpWho' => $dispatchData['Who_Is_Name'] ?? 'N/A',
                'tmpDelBy' => $dispatchData['Deleted_By_Name'] ?: 'N/A',
                'tmpDelInfo' => $dispatchData['Deleted_Reason'] ?: 'N/A',
                'tmpAppNotes' => $dispatchData['Notes'] ?: 'No notes provided.',
                'tmpAppFinNotes' => $dispatchData['Final_Notes'] ?: 'No final notes provided.',
                'img_path' => config('app.url').'/assets/image',
                'tmpApprovedTime' => $dispatchData['Approved_Time'] ?: 'N/A',
                'tmpArrivedTime' => $dispatchData['Arrived_Time'] ?: 'N/A',
                'tmpSessionTime' => $dispatchData['Session_Time'] ?: 'N/A',
                'tmpDoneTime' => $dispatchData['Done_Time'] ?: 'N/A',
                'tmpDeclTime' => $dispatchData['Declined_Time'] ?: 'N/A',
            ],
        ];

        $emailResponse = $this->mailService->sendEmailRequest($emailRequestData);


        if ($emailResponse['status'] === 'success') {
            return [
                'status' => 'success',
                'message' => 'Email notification sent successfully.',
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Failed to send email notification.',
            'details' => $emailResponse['error'] ?? 'Unknown error occurred.',
        ];
    }

    /**
     * Get Dispatch Chart Data and Format Results
     *
     * @param  int  $dept_ID  Department ID
     * @param  int  $idVal  Record ID
     * @return array Formatted data
     */
    private function getDispatchChartData(int $dept_ID, int $idVal): array
    {
        $records = DispatchChart::query()
            ->join('Dispatch_Location as dl', 'dl.Location_GUID', '=', 'Dispatch_Chart.App_Location_GUID')
            ->join('Dispatch_Building as db', 'db.Building_GUID', '=', 'Dispatch_Chart.App_Building_GUID')
            ->where('Dispatch_Chart.Company_Dept_ID', $dept_ID)
            ->where('Dispatch_Chart.ID', $idVal)
            ->select('Dispatch_Chart.*', 'dl.name as Location_Name', 'db.name as Building_Name')
            ->get();

        $formatDate = fn ($date) => $date ? date('m/d/Y g:i a', strtotime($date)) : '';
        $formatValDate = fn ($date) => $date ? date('m/d/Y H:i:s', strtotime($date)) : '';

        return $records->map(function ($data) use ($dept_ID, $formatDate, $formatValDate) {
            $approvedBy = '';
            if (! empty($data->Approved_by_Staff)) {
                [$approvedByFN, $approvedByLN] = Helper::getUserFirstLastName($dept_ID, $data->Approved_by_Staff);
                $approvedBy = "$approvedByLN, $approvedByFN";
            }

            return [
                'ID' => $data->ID,
                'App_Time' => $formatDate($data->App_Time),
                'Job' => $data->App_Service,
                'Building_Name' => $data->Building_Name,
                'Location_Name' => $data->Location_Name,
                'App_Approved' => $data->App_Approved,
                'App_Arrived' => $data->App_Arrived,
                'App_Session' => $data->App_Session,
                'Visit_Type' => $data->App_Visit_Type,
                'Who_Is_Name' => $data->Who_Is_Name,
                'Req_EMail' => $data->Req_EMail,
                'App_Declined' => $data->App_Declined,
                'App_Done' => $data->App_Done,
                'Approved_By' => $approvedBy,
                'Pat_MRN' => $data->Pat_MRN,
                'Req_Time' => $formatDate($data->Req_Time),
                'Approved_Time' => $formatDate($data->Approved_Time),
                'Approved_ValTime' => $formatValDate($data->Approved_Time),
                'Arrived_Time' => $formatDate($data->Arrived_Time),
                'Arrived_ValTime' => $formatValDate($data->Arrived_Time),
                'Session_Time' => $formatDate($data->Session_Time),
                'Session_ValTime' => $formatValDate($data->Session_Time),
                'Done_Time' => $formatDate($data->Done_Time),
                'Done_ValTime' => $formatValDate($data->Done_Time),
                'Declined_Time' => $formatDate($data->Declined_Time),
                'Declined_ValTime' => $formatValDate($data->Declined_Time),
                'Notes' => $data->Notes,
                'Final_Notes' => $data->Final_Notes,
                'LocDetail' => $data->App_LocDetail,
                'Priority' => $data->Priority,
                'Deleted_By_Name' => $data->Deleted_By_Name,
                'Deleted_Reason' => $data->Deleted_Reason,
            ];
        })->toArray();
    }
}
