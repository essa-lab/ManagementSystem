<?php

namespace App\Http\Resources\Resource;

use App\Http\Resources\PatronResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RenewRequestResource extends JsonResource
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
            
            'circulation_id' => $this->circulation_id,

            'circulation'=>new CirculationResource($this->whenLoaded('circulation')),


            'action_date' => $this->action_date,

            'action_type' => $this->status,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
