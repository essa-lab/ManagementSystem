<?php

namespace App\Http\Resources\Meilisearch;

use App\Http\Resources\LibraryResource;
use App\Http\Resources\Resource\CuratorResource;
use App\Http\Resources\Resource\EditorResource;
use App\Http\Resources\Resource\LanguageResource;
use App\Http\Resources\Resource\MediaResource;
use App\Http\Resources\Resource\SubjectResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class MeilisearchResource extends JsonResource
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

            'curators' => $this->whenLoaded('curators', function () {
                return $this->grouped_curators->map(fn ($curators) => CuratorResource::collection($curators));
            }),  
            'editors' => $this->whenLoaded('editors', function () {
                return $this->grouped_editors->map(fn ($editors) => EditorResource::collection($editors));
            }), 
            'medias' => $this->whenLoaded('medias', function () {
                return $this->grouped_medias->map(fn ($medias) => MediaResource::collection($medias));
            }),        
               
            'resourceable_type' => class_basename($this->resourceable_type),
            'resourceable' => $this->when($this->resourceable, function () {
                if ($this->resourceable instanceof \App\Models\Book\Book) {
                    return new MeilisearchBookResource($this->resourceable);
                } elseif ($this->resourceable instanceof \App\Models\Article\Article) {
                    return new MeilisearchArticleResource($this->resourceable);
                } elseif ($this->resourceable instanceof \App\Models\Research\Research) {
                    return new MeilisearchResearchResource($this->resourceable);
                }elseif ($this->resourceable instanceof \App\Models\DigitalResource\DigitalResource) {
                    return new MeilisearchDigitalResource($this->resourceable);
                }
                return null;
            }),
        ];    }

}
