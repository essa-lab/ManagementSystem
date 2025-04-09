<?php

namespace App\Http\Resources\Book;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookTranslatorResource extends JsonResource
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
            'translate_type_id'=>$this->translate_type_id,
            'translate_type'=>new TranslationTypeResource($this->whenLoaded('type')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
