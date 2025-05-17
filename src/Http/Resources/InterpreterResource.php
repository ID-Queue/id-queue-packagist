<?php

namespace IdQueue\IdQueuePackagist\Http\Resources;

use Carbon\Carbon;
use IdQueue\IdQueuePackagist\Enums\UserStatus;
use IdQueue\IdQueuePackagist\Models\Company\DispatchBuilding;
use Illuminate\Http\Resources\Json\JsonResource;

class InterpreterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray($request)
    {
        // Ensure resource is available and has the 'GUID' property
        $status = $this->resource->getStatus();

        return [
            'guid' => $this->resource->GUID,
            'staff_name' => "{$this->resource->First_name} {$this->resource->Last_name}",
            'location' => $this->formatStaffLocation($this->resource->Staff_Login_Location, $this->resource->Company_Dept_ID),
            // 'last_location' => ((bool) $this->resource->isStationed) ? StationedResource::collection($this->staffStation->load(['location', 'zone', 'building'])) : new LastLocationResource($this->lastLocation->load(['location', 'zone', 'building'])),
            'last_location' => $this->resource->isStationed && $this->resource->staffStation()->exists()
            ? StationedResource::collection($this->staffStation->load(['location', 'zone', 'building']))
            : ($this->lastLocation()->exists()
                ? new LastLocationResource($this->lastLocation->load(['location', 'zone', 'building']))
                : null),

            'time' => $this->formatLastActionTime($this->resource->Staff_Last_Action),
            'status' => ((bool) $this->resource->isStationed) ? UserStatus::note(UserStatus::Stationed) : UserStatus::note($status),
            'stationed' => (bool) $this->resource->isStationed,
            'stopwatch' => $this->calculateRequestTimer($this->resource->Staff_Last_Action),
            'icon' => UserStatus::image($status),
            'is_location_null' => $this->getLocationNull(),
        ];

    }

    private function getLocationNull(): bool
    {
        switch ($this->resource->Staff_Login_State) {
            case 1:
                return !$this->lastLocation()
                    ->whereDate('Location_Time', Carbon::today())
                    ->exists();
            case 2:
            case 3:
                return true;
            default:
                return false;
        }
    }
    
    
    /**
     * Format the last action time as an array [time, date].
     */
    private function formatLastActionTime(?Carbon $lastAction): array
    {
        if (! $lastAction instanceof \Carbon\Carbon) {
            return ['', ''];
        }

        return [
            $lastAction->format('g:i A'), // Time
            $lastAction->format('n/d/Y'), // Date
        ];
    }

    /**
     * Calculate the time difference from the last action to now.
     */
    private function calculateRequestTimer(?Carbon $lastAction): string
    {
        if (! $lastAction instanceof \Carbon\Carbon) {
            return '';
        }

        return $this->calculateTimeDifference($lastAction, Carbon::now());
    }

    /**
     * Calculate the difference between two times in HH:MM:SS format.
     */
    private function calculateTimeDifference(Carbon $startTime, Carbon $endTime): string
    {
        $totalSeconds = $endTime->diffInSeconds($startTime);

        $hours = intdiv($totalSeconds, 3600);
        $minutes = intdiv($totalSeconds % 3600, 60);
        $seconds = $totalSeconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    /**
     * Format the staff location based on the building IDs.
     */
    private function formatStaffLocation(?string $staffLoc, ?string $dept_ID): string
    {
        if (! $staffLoc) {
            return '';
        }

        if ($staffLoc === 'All') {
            return 'All';
        }

        $buildings = DispatchBuilding::getBuildingsByID($dept_ID, $staffLoc);

        return $buildings === [] ? '' : implode(', ', $buildings);
    }
}
