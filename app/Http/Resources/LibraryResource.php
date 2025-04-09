<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LibraryResource extends JsonResource
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
            'users'=>UserResource::collection($this->whenLoaded('users')),
            
            
            'name_ku'=>$this->name_ku,
            'name_ar'=>$this->name_ar,
            'name_en'=>$this->name_en,

            'name'=> $this->{'name_' . app()->getLocale()},
            'location' => $this->location,
            'logo'=>$this->logo_path,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

           
        ];
    }
}
