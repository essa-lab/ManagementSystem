<?php

namespace App\Http\Resources\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EditorResource extends JsonResource
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
            'type'=>$this->type,
            'content'=>$this->content,
            'language'=>$this->language,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
