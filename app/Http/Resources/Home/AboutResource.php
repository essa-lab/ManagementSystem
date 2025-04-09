<?php

namespace App\Http\Resources\Home;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AboutResource extends JsonResource
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
            'title_ar'=>$this->title_ar,
            'title_en'=>$this->title_en,
            'title_ku'=>$this->title_ku,

            'title'=> $this->{'title_' . app()->getLocale()},
            'content_ar'=>$this->content_ar,
            'content_en'=>$this->content_en,
            'content_ku'=>$this->content_ku,

            'content'=> $this->{'content_' . app()->getLocale()},

            'image'=>$this->image_path,

            
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
