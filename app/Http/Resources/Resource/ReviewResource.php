<?php

namespace App\Http\Resources\Resource;

use App\Http\Resources\LibraryResource;
use App\Http\Resources\PatronResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            
            // 'patron_id'=>$this->patron_id,
            'patron_name'=>$this->patron?->name,

            'resource_id'=>$this->resource_id,
            'resource'=>new ResourceResource($this->whenLoaded('resource')),

            'rate'=>$this->rate,
            'review'=>$this->review,
            'available'=>$this->available,                    
            'created_at' => $this->created_at,
        ];
    }
}
