<?php

namespace App\Http\Resources\Resource;

use App\Http\Resources\LibraryResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PenaltyValueResource extends JsonResource
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

            'created_user'=>new UserResource($this->whenLoaded('createdBy')),
            
            
            'amount'=>$this->amount,
                    
            'created_at' => $this->created_at,
        ];
    }
}
