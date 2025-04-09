<?php

namespace App\Http\Resources\DigitalResource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DigitalResource extends JsonResource
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
            'publisher'=>$this->publisher,
            'coverage'=>$this->coverage,
            'relations'=>DigitalRelationResource::collection($this->whenLoaded('relations')),
            'right'=>new DigitalRightResource($this->whenLoaded('right')),
            'format'=>new DigitalFormatResource($this->whenLoaded('digitalFormat')),
            'type'=>new DigitalTypeResource($this->whenLoaded('digitalType')),
            'specific_subject'=>new DigitalSpecificSubjectResource($this->whenLoaded('specificSubject')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
