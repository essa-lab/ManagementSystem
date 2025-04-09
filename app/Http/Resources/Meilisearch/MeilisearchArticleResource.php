<?php

namespace App\Http\Resources\Meilisearch;


use App\Http\Resources\Article\ArticleKeywordResource;
use App\Http\Resources\Article\ArticleTypeResource;
use App\Http\Resources\Article\ScientificBranchesResource;
use App\Http\Resources\Article\SpecificationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeilisearchArticleResource extends JsonResource
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
            'keyword'=>ArticleKeywordResource::collection($this->whenLoaded('articleKeyword')),
            'type'=>new ArticleTypeResource($this->whenLoaded('articleType')),
            'scientific_clasification'=>new ScientificBranchesResource($this->whenLoaded('articleScientificClassification')),
            'specification'=>new SpecificationResource($this->whenLoaded('articleSpecification')),
        ];
    }
}
