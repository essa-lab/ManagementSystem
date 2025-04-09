<?php
namespace App\Http\Resources\Aquisition;

use App\Http\Resources\LibraryResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'po_number' => $this->po_number,
            'supplier_name' => $this->supplier_name,
            'contact_number' => $this->contact_number,
            'order_date' => $this->date,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'created_user'=>new UserResource($this->whenLoaded('createdBy')),
            'total_order_cost' => $this->total_order_cost,
            'approved_by' => $this->approved_by,
            'approved_user'=>new UserResource($this->whenLoaded('approvedBy')),
            'library_id'=>$this->library_id,
            'library'=>new LibraryResource($this->whenLoaded('library')),
            'note' => $this->note,
            'items' => OrderItemResource::collection($this->whenLoaded('orderItems')),
            'status_logs' => OrderLogResource::collection($this->whenLoaded('logs')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
