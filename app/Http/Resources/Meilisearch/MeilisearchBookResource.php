<?php

namespace App\Http\Resources\Meilisearch;

use App\Http\Resources\Book\BookSpecificSubjectResource;
use App\Http\Resources\Book\TranslationTypeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeilisearchBookResource extends JsonResource
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
            'registeration_number' => $this->registeration_number,
            'order_number' => $this->order_number,
            'barcode' => $this->barcode,
            'isbn' => $this->isbn,
            'translator_type'=>new TranslationTypeResource($this->whenLoaded('bookTranslateType')),
            'specific_subject'=>BookSpecificSubjectResource::collection($this->whenLoaded('specificSubjects')),

        ];
    }
}
