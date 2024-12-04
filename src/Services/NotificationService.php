<?php

namespace IdQueue\IdQueuePackagist\Services;

use IdQueue\IdQueuePackagist\Models\Company\DeptPreSetting;
use IdQueue\IdQueuePackagist\Utils\Helper;
use Illuminate\Support\Facades\DB;
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
        $emails = DB::table('User_Accounts AS ua')
            ->join('Dispatch_Staff AS ds', 'ds.Acc_GUID', '=', 'ua.GUID')
            ->where('ua.Company_Dept_ID', $deptId)
            ->where('ds.Company_Dept_ID', $deptId)
            ->where('ds.Service', $serviceName)
            ->whereNotNull('ua.email')
            ->where('ua.email_when_req', 1)
            ->where(function ($query) {
                $query->where('ua.Account_Deleted', 0)
                    ->orWhereNull('ua.Account_Deleted');
            })
            ->pluck('ua.email');

        $this->mailService->staffEmailRequest([
            'emails' => $emails,
            'type' => 'Submit',
            'data' => [
                'url' => $url, // dynamic data
            ],
        ]);

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
        $deptSettings = DeptPreSetting::where('Company_Dept_ID',$deptId)
            ->select('Service_Single', 'Staff_Single', 'Location_Single', 'Building_Single', 'Person_ID')
            ->first();
        $emailRequestData = [
            'to' => $dispatchData['Req_EMail'] ?? 'default-email@example.com',
            'subject' => $subject,
            'msg' => 'This is a follow-up message.',
            'details' => [
                'hdr' => "ID-Queue Info: Request Follow-up for #$idVal",
                'bdy' => 'We are pleased to inform you that your request was successfully submitted.',
                'tmpID' => $dispatchData['ID'],
                'tmpReqTime' => $dispatchData['Req_Time'] ?? 'N/A',
                'tmpBld' => $dispatchData['Building_Name'] ??  $deptSettings->Building_Single,
                'tmpLoc' => $dispatchData['Location_Name'] ??  $deptSettings->Location_Single,
                'locationSingle' => $deptSettings->Location_Single,
                'personID' =>  $deptSettings->Person_ID,
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
        $records = DB::table('Dispatch_Chart AS dc')
            ->join('Dispatch_Location AS dl', 'dl.Location_GUID', '=', 'dc.App_Location_GUID')
            ->join('Dispatch_Building AS db', 'db.Building_GUID', '=', 'dc.App_Building_GUID')
            ->where('dc.Company_Dept_ID', $dept_ID)
            ->where('dc.ID', $idVal)
            ->select('dc.*', 'dl.name AS Location_Name', 'db.name AS Building_Name')
            ->get()
            ->toArray();

        $formatDate = fn ($date) => $date ? date('m/d/Y g:i a', strtotime($date)) : '';
        $formatValDate = fn ($date) => $date ? date('m/d/Y H:i:s', strtotime($date)) : '';

        return array_map(function ($data) use ($dept_ID, $formatDate, $formatValDate) {
            $tmpApprBy = '';
            if (! empty($data->Approved_by_Staff)) {
                [$tmpApprByFN, $tmpApprByLN] = Helper::getUserFirstLastName($dept_ID, $data->Approved_by_Staff);
                $tmpApprBy = "$tmpApprByLN, $tmpApprByFN";
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
                'Approved_By' => $tmpApprBy,
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
        }, $records);
    }
}
