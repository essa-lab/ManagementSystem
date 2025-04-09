<?php

namespace App\Http\Resources\Resource;

use App\Http\Resources\PatronResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckPenaltyResource extends JsonResource
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
            
            'resource_copy_id' => $this->resource_copy_id,
            'resource_copy'=>new ResourceCopyResource($this->whenLoaded('resourceCopy')),
            'patron_id'=>$this->patron_id,
            'patron'=>new PatronResource($this->whenLoaded('patron')),
            'borrow_date' => $this->borrow_date,
            'due_date' => $this->due_date,
            'return_date' => $this->return_date,
            'status' => $this->status,
            'circulation_count' => $this->circulation_count,
            'penalty'=>new PenaltryResource($this->whenLoaded('latestPenalty')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
