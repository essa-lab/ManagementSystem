<?php

namespace App\Http\Resources\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'resource_id' => $this->resource_id,

            'availability' => $this->availability,
            'max_allowed_day' => $this->max_allowed_day,
            'allow_renewal' => $this->allow_renewal,
            'renewal_cycle' => $this->renewal_cycle,

            'locked' => $this->locked,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
