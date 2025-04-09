<?php
namespace App\Http\Resources\Aquisition;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'purchase_order_id' => $this->purchase_order_id,
            'status' => $this->status,
            'changed_by' => $this->changed_by,
            'user'=>new UserResource($this->whenLoaded('changedBy')),
            'changed_at' => $this->changed_at,
        ];
    }
}
