<?php

namespace App\Http\Resources\Resource;

use App\Http\Resources\LibraryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceCounterLibraryAndLanguageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'library'=>new LibraryResource($this->library),
            'lanuage'=>new LanguageResource($this->language),
            'resource_count' => $this->total_resources,
        ];
    }
}
