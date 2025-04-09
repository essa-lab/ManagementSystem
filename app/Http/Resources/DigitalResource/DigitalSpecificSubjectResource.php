<?php

namespace App\Http\Resources\DigitalResource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DigitalSpecificSubjectResource extends JsonResource
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
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'title_ku' => $this->title_ku,
            'title' => $this->{'title_' . app()->getLocale()},

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
