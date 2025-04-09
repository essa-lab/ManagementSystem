<?php

namespace App\Http\Resources\Meilisearch;

use App\Http\Resources\DigitalResource\DigitalFormatResource;
use App\Http\Resources\DigitalResource\DigitalSpecificSubjectResource;
use App\Http\Resources\DigitalResource\DigitalTypeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeilisearchDigitalResource extends JsonResource
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
            'identifier'=>$this->identifier,

            'format'=>new DigitalFormatResource($this->whenLoaded('digitalFormat')),
            'type'=>new DigitalTypeResource($this->whenLoaded('digitalType')),
            'specific_subject'=>new DigitalSpecificSubjectResource($this->whenLoaded('specificSubject')),
            
        ];
    }
}
