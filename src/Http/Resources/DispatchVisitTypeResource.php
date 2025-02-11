<?php

namespace IdQueue\IdQueuePackagist\Http\Resources;

use IdQueue\IdQueuePackagist\Models\Company\DispatchBuilding;
use IdQueue\IdQueuePackagist\Models\Company\DispatchLocation;
use IdQueue\IdQueuePackagist\Models\Company\DispatchZone;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class DispatchVisitTypeResource extends JsonResource
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
            'time_complete' => $this->resource->time_complete,
            'Visit_Type_Enabled' => $this->resource->Visit_Type_Enabled,
            'Visit_Type_Priority' => $this->resource->Visit_Type_Priority,
            'first_location' => $this->formatLocations($this->resource->first_location, DispatchBuilding::class, 'Building_GUID'),
            'second_location' => $this->formatLocations($this->resource->second_location, DispatchZone::class, 'Zone_GUID'),
            'third_location' => $this->formatLocations($this->resource->third_location, DispatchLocation::class, 'Location_GUID'),
        ];
    }

    /**
     * Format locations based on the given model and column.
     *
     * @param  string  $model
     * @param  string  $column
     */
    private function formatLocations(?string $location, $model, $column): Collection
    {
        if (empty($location)) {
            return collect(); // Return an empty collection for consistency
        }

        $ids = explode(',', $location);

        return $model::whereIn($column, $ids)->get();
    }
}
