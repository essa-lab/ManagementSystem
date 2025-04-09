<?php

namespace App\Http\Resources\Home;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AboutContentResource extends JsonResource
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
            'description_ar'=>$this->description_ar,
            'description_en'=>$this->description_en,
            'description_ku'=>$this->description_ku,

            'description'=> $this->{'description_' . app()->getLocale()},

            'location_title_ar'=>$this->location_title_ar,
            'location_title_en'=>$this->location_title_en,
            'location_title_ku'=>$this->location_title_ku,

            'location_title'=> $this->{'location_title_' . app()->getLocale()},

            'location_description_ar'=>$this->location_description_ar,
            'location_description_en'=>$this->location_description_en,
            'location_description_ku'=>$this->location_description_ku,

            'location_description'=> $this->{'location_description_' . app()->getLocale()},


            'coordinates'=>$this->coordinates,

            
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
