<?php

namespace App\Models\Article;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ArticleKeyword extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'title_en',
        'title_ku',
        'title_ar',
        'article_id'
    ];

    public function article(){
        return $this->belongsTo(Article::class,'article_id');
    }

}
