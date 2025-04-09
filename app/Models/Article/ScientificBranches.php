<?php

namespace App\Models\Article;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ScientificBranches extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'title_en',
        'title_ku',
        'title_ar',
    ];

    public function article(){
        return $this->hasMany(Article::class);
    }
}
