<?php

namespace App\Http\Resources\Meilisearch;

use App\Http\Resources\Research\EducationLevelResource;
use App\Http\Resources\Research\ResearchFormatResource;
use App\Http\Resources\Research\ResearchKeywordResource;
use App\Http\Resources\Research\ResearchTypeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeilisearchResearchResource extends JsonResource
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
            'registeration_number'=>$this->registeration_number,
            'order_number'=>$this->order_number,
            'keyword'=>ResearchKeywordResource::collection($this->whenLoaded('researchKeywords')),
            'type'=>new ResearchTypeResource($this->whenLoaded('researchType')),
            'format'=>new ResearchFormatResource($this->whenLoaded('researchFormat')),
            'education_level'=>new EducationLevelResource($this->whenLoaded('researchEducationLevel')),
            
        ];
    }
}
