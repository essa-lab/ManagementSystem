<?php

namespace App\Http\Resources\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CuratorEducationResource extends JsonResource
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
            'university_ar' => $this->university_ar,
            'university_en' => $this->university_en,
            'university_ku' => $this->university_ku,

            'university' => $this->{'university_' . app()->getLocale()},

            'college_ar' => $this->college_ar,
            'college_en' => $this->college_en,
            'college_ku' => $this->college_ku,

            'college' => $this->{'college_' . app()->getLocale()},

            'education_major_ar' => $this->education_major_ar,
            'education_major_en' => $this->education_major_en,
            'education_major_ku' => $this->education_major_ku,

            'education_major' => $this->{'education_major_' . app()->getLocale()},

            'scientific_department_ar' => $this->scientific_department_ar,
            'scientific_department_en' => $this->scientific_department_en,
            'scientific_department_ku' => $this->scientific_department_ku,

            'scientific_department' => $this->{'scientific_department_' . app()->getLocale()},





            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
