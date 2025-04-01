<?php

namespace IdQueue\IdQueuePackagist\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestLifeLineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'ID' => $this->resource->ID,
            'Company_Dept_ID' => $this->resource->Company_Dept_ID,
            'name' => $this->resource->name,
            'Action_Time' => Carbon::parse($this->resource->Action_Time)->format('Y-m-d H:i'),
            'Action_Taken' => $this->image($this->resource->Action_Taken),
            'Request_ID' => $this->resource->Request_ID,
        ];
    }

    public function image($AppAction): array
    {
        return match ($AppAction) {
            1 => [
                'image' => 'caution_25.png',
                'title' => 'Request not accepted yet.',
            ],
            2 => [
                'image' => 'theFinger_25.png',
                'title' => 'Request Accepted and on route.',
            ],
            3 => [
                'image' => 'sw.png',
                'title' => 'Arrived and waiting to start the session.',
            ],
            4 => [
                'image' => 'inSession_25.png',
                'title' => 'Session in Progress.',
            ],
            5 => [
                'image' => 'done_25.png',
                'title' => 'Session/Request Complete.',
            ],
            6 => [
                'image' => 'delete_25.png',
                'title' => 'Request Deleted.',
            ],
            7 => [
                'image' => 'pause_25.png',
                'title' => 'Paused Request.',
            ],
            8 => [
                'image' => 'unpause_25.png',
                'title' => 'Un-Paused Request.',
            ],
            9 => [
                'image' => 'theFingerRelease_25.png',
                'title' => 'Released Request.',
            ],
            10 => [
                'image' => 'edit_25.png',
                'title' => 'Edit to Request.',
            ],
            11 => [
                'image' => 'datetime_25.png',
                'title' => 'Pre-Scheduled appointment for Date/Time.',
            ],
            12 => [
                'image' => 'datetime_add_25.png',
                'title' => 'Pre-Schedule Appointment requested at...',
            ],
            13 => [
                'image' => 'video.png',
                'title' => 'Joined Private Video...',
            ],
            14 => [
                'image' => 'dispatcher_25.png',
                'title' => 'Dispatcher Assigned Request.',
            ],
            15 => [
                'image' => 'dispatcher_25_clock.png',
                'title' => 'Dispatcher Edit Request Time.',
            ],
            16 => [
                'image' => 'dispatcher_25_delete.png',
                'title' => 'Dispatcher Deleted Request.',
            ],
            17 => [
                'image' => 'dispatcher_25_note.png',
                'title' => 'Dispatcher Applied Note.',
            ],
            18 => [
                'image' => 'dispatcher_request_reassigned.png',
                'title' => 'Request Reassigned by Dispatcher',
            ],
            19 => [
                'image' => 'dispatcher_request_unassigned.png',
                'title' => 'Request Unassigned by Dispatcher',
            ],
            default => [
                'image' => 'unknown.png',
                'title' => 'Unknown Action.',
            ],
        };
    }
}
