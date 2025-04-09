<?php

namespace App\Models\Research;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ResearchKeyword extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'title_en',
        'title_ku',
        'title_ar',
        'research_id'
    ];

    public function research(){
        return $this->belongsTo(Research::class,'research_id');
    }

}
