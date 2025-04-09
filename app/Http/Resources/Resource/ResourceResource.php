<?php

namespace App\Http\Resources\Resource;

use App\Http\Resources\Article\ArticleResource;
use App\Http\Resources\Book\BookResource;
use App\Http\Resources\DigitalResource\DigitalResource;
use App\Http\Resources\LibraryResource;
use App\Http\Resources\Research\ResearchResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceResource extends JsonResource
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
            'created_by'=>$this->created_by,
            'creator'=> new UserResource($this->whenLoaded('createdBy')),
            'library_id'=>$this->library_id,
            'language_id'=>$this->language_id,
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
            'resource_source' => new SourceResource($this->whenLoaded('resourceSource')),

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
                    return new BookResource($this->resourceable);
                } elseif ($this->resourceable instanceof \App\Models\Article\Article) {
                    return new ArticleResource($this->resourceable);
                } elseif ($this->resourceable instanceof \App\Models\Research\Research) {
                    return new ResearchResource($this->resourceable);
                }elseif ($this->resourceable instanceof \App\Models\DigitalResource\DigitalResource) {
                    return new DigitalResource($this->resourceable);
                }
                return null;
            }),
            // 'related_resources'=>RelatedResourceResource::collection($this->related_resources??[])
        ];
    }
}
