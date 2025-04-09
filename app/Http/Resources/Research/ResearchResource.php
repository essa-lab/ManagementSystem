<?php

namespace App\Http\Resources\Research;

use App\Http\Resources\Research\ResearchFormatResource;
use App\Http\Resources\Research\ResearchTypeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResearchResource extends JsonResource
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
            'publish_date'=>$this->publish_date,
            'discussion_date'=>$this->discussion_date,
            'number_of_pages'=>$this->number_of_pages,
            'keyword'=>ResearchKeywordResource::collection($this->whenLoaded('researchKeywords')),
            'type'=>new ResearchTypeResource($this->whenLoaded('researchType')),
            'format'=>new ResearchFormatResource($this->whenLoaded('researchFormat')),
            'education_level'=>new EducationLevelResource($this->whenLoaded('researchEducationLevel')),
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'title_ku' => $this->title_ku,
            'title' => $this->{'title_' . app()->getLocale()},


            'university_ar' => $this->university_ar,
            'university_en' => $this->university_en,
            'university_ku' => $this->university_ku,
            'university' => $this->{'university_' . app()->getLocale()},

            'college_ar' => $this->college_ar,
            'college_en' => $this->college_en,
            'college_ku' => $this->college_ku,
            'college' => $this->{'college_' . app()->getLocale()},

            'education_major_ar' => $this->education_major_ar,
            'education_major_en' => $this->education_major_en,
            'education_major_ku' => $this->education_major_ku,
            'education_major' => $this->{'education_major_' . app()->getLocale()},

            'status' => $this->status,
            'classification' => $this->classification,
            'price' => $this->price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
