<?php

namespace App\Http\Resources\Article;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
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

            'subtitle_ar' => $this->subtitle_ar,
            'subtitle_en' => $this->subtitle_en,
            'subtitle_ku' => $this->subtitle_ku,
            'subtitle' => $this->{'subtitle_' . app()->getLocale()},

            'secondary_title_ar' => $this->secondary_title_ar,
            'secondary_title_en' => $this->secondary_title_en,
            'secondary_title_ku' => $this->secondary_title_ku,
            'secondary_title' => $this->{'secondary_title_' . app()->getLocale()},

            'publication_date'=>$this->publication_date,
            'research_year'=>$this->research_year,
            'duration_of_research'=>$this->duration_of_research,

            'course'=>$this->course,
            'number'=>$this->number,
            'map'=>$this->map,
            'place_of_printing'=>$this->place_of_printing,
            'journal_name'=>$this->journal_name,
            'journal_volume'=>$this->journal_volume,

            'number_of_pages'=>$this->number_of_pages,

            'keyword'=>ArticleKeywordResource::collection($this->whenLoaded('articleKeyword')),
            'type'=>new ArticleTypeResource($this->whenLoaded('articleType')),
            'scientific_clasification'=>new ScientificBranchesResource($this->whenLoaded('articleScientificClassification')),
            'specification'=>new SpecificationResource($this->whenLoaded('articleSpecification')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
