<?php

namespace App\Http\Resources\Resource;

use App\Http\Resources\LibraryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LibrarySettingsResource extends JsonResource
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
            'library_id'=>$this->library_id,
            'library'=>new LibraryResource($this->whenLoaded('library')),
            
            
            'schedular_time'=>$this->schedular_time,
            'penalty_value'=>$this->penalty_value,
                    
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
