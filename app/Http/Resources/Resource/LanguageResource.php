<?php

namespace App\Http\Resources\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LanguageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id??null,
            'title_ar' => $this->title_ar??null,
            'title_en' => $this->title_en??null,
            'title_ku' => $this->title_ku??null,

            'title' => $this->{'title_' . app()->getLocale()}??null,

            'created_at' => $this->created_at??null,
            'updated_at' => $this->updated_at??null,
        ];
    }
}
