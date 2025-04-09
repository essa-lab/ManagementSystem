<?php

namespace App\Models\Research;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ResearchFormat extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'title_en',
        'title_ku',
        'title_ar',
    ];

    public function research(){
        return $this->hasMany(Research::class);
    }
}
