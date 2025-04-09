<?php

namespace App\Http\Resources\Book;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookPrintInformationResource extends JsonResource
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
            
                    
            'print_house_ar' => $this->print_house_ar,
            'print_house_en' => $this->print_house_en,
            'print_house_ku' => $this->print_house_ku,
            'print_house' => $this->{'print_house_' . app()->getLocale()},


            'publisher_ar' => $this->publisher_ar,
            'publisher_en' => $this->publisher_en,
            'publisher_ku' => $this->publisher_ku,
            'publisher' => $this->{'publisher_' . app()->getLocale()},



            'print_location_ar' => $this->print_location_ar,
            'print_location_en' => $this->print_location_en,
            'print_location_ku' => $this->print_location_ku,
            'print_location' => $this->{'print_location_' . app()->getLocale()},


            'print_year' => $this->print_year,
            'year_type' => $this->year_type,

            'conditions' => PrintConditionResource::collection($this->whenLoaded('conditions')),

            'types' => PrintTypeResource::collection($this->whenLoaded('type')),
        ];
    }
}
