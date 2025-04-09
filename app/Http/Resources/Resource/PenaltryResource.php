<?php

namespace App\Http\Resources\Resource;

use App\Http\Resources\PatronResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PenaltryResource extends JsonResource
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

        'per_day'=>$this->how_much_per_day,
        'total'=>$this->total_penalty_amount,
        'is_paid'=>$this->is_paid,
        'days_overdue'=>$this->days_overdue,

            
            
        ];
    }
}
