<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'locale'=>$this->locale,
            'profile_picture'=>$this->profile_picture ? Storage::url($this->profile_picture) : null,
            'library_id'=>$this->library_id,
            'role'=>$this->role,
            // 'library_id'=>$this->library_id,
            'library' => new LibraryResource($this->whenLoaded('library')),
            'privilages'=>PrivilageResource::collection($this->whenLoaded('privilages')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
