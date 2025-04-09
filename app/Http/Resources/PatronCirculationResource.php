<?php

namespace App\Http\Resources;

use App\Http\Resources\Resource\ResourceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatronCirculationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return [
        'id'=>$this['id'],
        'resource'=>new ResourceResource($this['resource']),
        'review'=>$this['review'],
        'date_borrowed'=>$this['date_borrowed'],
        'date_renewed' => $this['date_renewed'],
        'due_date' => $this['due_date'],
        'status' => $this['status'],
        'penalty_amount' => $this['penalty_amount'],
        'penalty_cleared' => $this['penalty_cleared']
       ];
    }
    
}
