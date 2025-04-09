<?php

namespace App\Models\Article;

use App\Models\BaseModel;
use App\Models\Resource\Curator;
use App\Models\Resource\Resource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Article extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'registeration_number',
        'order_number',
        'subtitle_en',
        'subtitle_ku',
        'subtitle_ar',
        'secondary_title_en',
        'secondary_title_ku',
        'secondary_title_ar',
        'publication_date',
        'research_year',
        'duration_of_research',
        'course',
        'number',
        'number_of_pages',
        'map',
        'place_of_printing_en',
        'place_of_printing_ar',
        'place_of_printing_ku',
        'journal_name',
        'journal_volume',
        'article_scientific_classification_id',
        'article_type_id',
        'article_specification_id',
    ];

    public function resource()
    {
        return $this->morphOne(Resource::class, 'resourceable');
    }
    public function articleType(){
        return $this->belongsTo(ArticleType::class,'article_type_id');
    }
    public function articleSpecification(){
        return $this->belongsTo(Specification::class,'article_specification_id');
    }
    public function articleScientificClassification(){
        return $this->belongsTo(ScientificBranches::class,'article_scientific_classification_id');
    }
    public function articleKeyword(){
        return $this->hasMany(ArticleKeyword::class);
    }
   
    //boot

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($article) {
            $article->resource()->delete();
        });

      
        static::updated(function ($artcile) {
            $artcile->resource->searchable();
        });
    }

  


}
