<?php

namespace IdQueue\IdQueuePackagist\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'GUID' => $this->resource->GUID,
            'First_name' => $this->resource->First_name,
            'Last_name' => $this->resource->Last_name,
        ];
    }
}
