<?php

namespace App\Http\Resources\Resource;

use App\Http\Resources\Article\ArticleResource;
use App\Http\Resources\Book\BookResource;
use App\Http\Resources\DigitalResource\DigitalResource;
use App\Http\Resources\LibraryResource;
use App\Http\Resources\Research\ResearchResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RelatedResourceResource extends JsonResource
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
            'library' => new LibraryResource($this->whenLoaded('library')),
            'language' => new LanguageResource($this->whenLoaded('language')),
            'uuid' => $this->uuid,
            'link' => $this->link,
            'registry_date' => $this->registry_date,
            'arrival_date' => $this->arrival_date,
            'number_of_copies' => $this->number_of_copies,
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'title_ku' => $this->title_ku,

            'title' => $this->{'title_' . app()->getLocale()},

            'subjects' => SubjectResource::collection($this->whenLoaded('subjects')),

            'medias' => $this->whenLoaded('medias', function () {
                return $this->grouped_medias->map(fn ($medias) => MediaResource::collection($medias));
            }),        
               
            
        ];
    }
}
