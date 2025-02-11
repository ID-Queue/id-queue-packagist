<?php

namespace IdQueue\IdQueuePackagist\Http\Resources;

use DateTime;
use DateTimeZone;
use Exception;
use IdQueue\IdQueuePackagist\Models\Company\DeptPreSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceCustomizationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     *
     * @throws Exception
     */
    public function toArray($request): array
    {
        $deptSettings = DeptPreSetting::where('Company_Dept_ID', $request->input('d_id'))->first();

        return [
            'department_title' => [
                'label' => 'Department Title',
                'value' => $deptSettings->getAttribute('Company_Dept'),
            ],
            'service_identifier' => [
                'label' => 'Service Identifier',
                'value' => $deptSettings->getAttribute('Service_Single'),
            ],
            'staff_identifier' => [
                'label' => 'Staff Identifier',
                'value' => $deptSettings->getAttribute('Staff_Single'),
            ],
            'first_location' => [
                'label' => 'First Location',
                'value' => $deptSettings->getAttribute('Building_Single'),
            ],
            'second_location' => [
                'label' => 'Second Location',
                'value' => $deptSettings->getAttribute('Zone_Single'),
            ],
            'third_location' => [
                'label' => 'Third Location',
                'value' => $deptSettings->getAttribute('Location_Single'),
            ],
            'visit_types' => [
                'label' => 'Visit Type',
                'value' => '',
            ],
            'cancel_request_reason' => [
                'label' => 'Cancel Request Reason',
                'value' => '',
            ],
            'requester_identifier' => [
                'label' => 'Requester Identifier',
                'value' => $deptSettings->getAttribute('Requester_ID'),
            ],
            'person_identifier' => [
                'label' => 'Person Identifier',
                'value' => $deptSettings->getAttribute('Person_ID'),
            ],
            'default_timezone' => [
                'label' => 'Default TimeZone',
                'value' => date_default_timezone_get(),
                'timezones' => [
                    'value' => $this->getTimeZones(),
                ],
            ],
        ];
    }

    public function formatOffset($offset): string
    {
        $hours = floor(abs($offset) / 3600);
        $minutes = (abs($offset) % 3600) / 60;
        $sign = ($offset < 0) ? '-' : '+';

        return sprintf('%s%02d:%02d', $sign, $hours, $minutes);
    }

    /**
     * @throws Exception
     */
    public function getTimeZones(): array
    {
        $utc = new DateTimeZone('UTC');
        $dt = new DateTime('now', $utc);
        $timezones = [];

        foreach (DateTimeZone::listIdentifiers() as $tz) {
            $currentTz = new DateTimeZone($tz);
            $offset = $currentTz->getOffset($dt);
            $transition = $currentTz->getTransitions($dt->getTimestamp(), $dt->getTimestamp());
            $abbr = $transition[0]['abbr'];
            $timezones[] = [
                'timezone' => $tz,
                'abbreviation' => $abbr,
                'offset' => $this->formatOffset($offset),
                'default' => ($tz == date_default_timezone_get()),
            ];
        }

        return $timezones;
    }
}
