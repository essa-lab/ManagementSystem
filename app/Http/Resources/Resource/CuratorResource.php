<?php

namespace App\Http\Resources\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CuratorResource extends JsonResource
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
            
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'name_ku' => $this->name_ku,

            'name' => $this->{'name_' . app()->getLocale()},
            'type' => $this->type,

            'education_level'=>new CuratorEducationResource($this->whenLoaded('education')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
