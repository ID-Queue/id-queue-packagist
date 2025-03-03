<?php

namespace IdQueue\IdQueuePackagist\Http\Resources;

use IdQueue\IdQueuePackagist\Enums\AppSettings;
use IdQueue\IdQueuePackagist\Models\Company\AdminServiceSetting;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class LastLocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        $location = [
            'location' => $this->location?->name ?? null,
        ];
    
        $settings = [
            AppSettings::App_Zone_Show => 'zone',
            AppSettings::App_Building_Show => 'building',
        ];
    
        foreach ($settings as $setting => $key) {
            if (AdminServiceSetting::getSettingFor($setting)) {
                $location[$key] = $this->{$key}?->name ?? null;
            }
        }
    
        return $location;
    }
}
