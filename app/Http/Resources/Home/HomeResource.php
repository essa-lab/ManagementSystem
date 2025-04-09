<?php

namespace App\Http\Resources\Home;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeResource extends JsonResource
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
            'subtitle_ar'=>$this->subtitle_ar,
            'subtitle_en'=>$this->subtitle_en,
            'subtitle_ku'=>$this->subtitle_ku,

            'subtitle'=> $this->{'subtitle_' . app()->getLocale()},

            'asset'=>$this->asset_path,

            
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
