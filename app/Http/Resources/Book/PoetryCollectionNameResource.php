<?php

namespace App\Http\Resources\Book;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PoetryCollectionNameResource extends JsonResource
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
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'name_ku' => $this->name_ku,
            'name' => $this->{'name_' . app()->getLocale()},
            'poetry_collection_id'=>$this->poetry_collection_id,
            'poetry_collection'=>new PoetryCollectionResource($this->whenLoaded('poetryCollection')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
