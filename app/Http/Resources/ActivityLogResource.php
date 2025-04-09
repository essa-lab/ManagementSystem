<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'type' => class_basename($this->loggable_type),
            'entity_id' => $this->loggable_id,
            
            'user_id' => $this->user_id??null,
            'user'=>new UserResource($this->whenLoaded('user')),
            
            'action' => $this->action,
            'old_values' => $this->old_values ?? null,
            'new_values' => $this->new_values ?? null,
            'changes' => $this->changes ?? null,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'performed_at' => $this->performed_at->toIso8601String(),
        ];
    }
}
