<?php

namespace IdQueue\IdQueuePackagist\Http\Resources;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class InterPreterResourceList extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return [
            'interpreters' => [
                'available' => $this->mapAvailableInterpreters($this->resource->users['available'] ?? []),
                'arrived' => $this->mapAvailableInterpreters($this->resource->users['arrived'] ?? []),
                'dispatched' => $this->mapAvailableInterpreters($this->resource->users['dispatched'] ?? []),
                'session' => $this->mapAvailableInterpreters($this->resource->users['session'] ?? []),
                'paused' => $this->mapAvailableInterpreters($this->resource->users['paused'] ?? []),
                'stationed' => $this->mapAvailableInterpreters($this->resource->users['stationed'] ?? []),
                'accepted' => $this->mapAvailableInterpreters($this->resource->users['accepted'] ?? []),
                'lunchandna' => $this->mapAvailableInterpreters($this->resource->users['lunchandna'] ?? []),
            ]
        ];

    }

    /**
     * Helper to map available interpreters.
     */
    private function mapAvailableInterpreters(Collection $interpreters): AnonymousResourceCollection
    {

        return InterpreterResource::collection($interpreters);
    }
}
