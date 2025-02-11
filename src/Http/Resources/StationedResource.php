<?php

namespace IdQueue\IdQueuePackagist\Http\Resources;

use IdQueue\IdQueuePackagist\Enums\AppSettings;
use IdQueue\IdQueuePackagist\Models\Company\AdminServiceSetting;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class StationedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $location = [
            'location' => null,
        ];
        if (isset($this->location)) {
            $location['location'] = $this->location->name;
        }

        $settings = [
            AppSettings::App_Zone_Show => 'zone',
            AppSettings::App_Building_Show => 'building',
        ];

        foreach ($settings as $setting => $key) {
            if (AdminServiceSetting::getSettingFor($setting)) {
                $location[$key] = null;
                if (isset($this->{$key})) {
                    $location[$key] = $this->{$key}->name;
                }
            }
        }

        return $location;

    }
}
