<?php

namespace App\Models\Research;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class EducationLevel extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'title_ar',
        'title_en',
        'title_ku'
    ];

    public function research(){
        return $this->hasMany(Research::class);
    }

}
