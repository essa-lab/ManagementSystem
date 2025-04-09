<?php

namespace App\Models\Resource;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CuratorEducation extends BaseModel
{
    use HasFactory;
    protected $table = 'curator_educations';
    protected $fillable = [
        
        'scientific_department_en',
        'scientific_department_ku',
        'scientific_department_ar',
        'university_en',
        'university_ku',
        'university_ar',
        'college_en',
        'college_ku',
        'college_ar',
        'education_major_en',
        'education_major_ku',
        'education_major_ar',
        'curator_id'
    ];

    public function curator(){
        return $this->belongsTo(Curator::class,'curator_id');
    }


}
