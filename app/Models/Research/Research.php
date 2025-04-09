<?php

namespace App\Models\Research;

use App\Models\BaseModel;
use App\Models\Resource\Curator;
use App\Models\Resource\Resource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Research extends BaseModel
{
    protected $table="researches";
    use HasFactory;
    protected $fillable = [
        'registeration_number',
        'order_number',
        'publish_date',
        'discussion_date',
        'number_of_pages',
        'price',
        'classification',
        'status',
        'education_level_id',
        'research_type_id',
        'research_format_id',
        'university_en',
        'university_ar',
        'university_ku',

        'college_en',
        'college_ku',
        'college_ar',
        'education_major_en',
        'education_major_ku',
        'education_major_ar'
    ];

    public function resource()
    {
        return $this->morphOne(Resource::class, 'resourceable');
    }
    public function researchEducationLevel(){
        return $this->belongsTo(EducationLevel::class,'education_level_id');
    }
    public function researchType(){
        return $this->belongsTo(ResearchType::class,'research_type_id');
    }
    public function researchFormat(){
        return $this->belongsTo(ResearchFormat::class,'research_format_id');
    }
    public function researchKeywords(){
        return $this->hasMany(ResearchKeyword::class);
    }
    public function firstSupervisor()
    {
        return $this->hasOneThrough(Curator::class, Resource::class, 'resourceable_id', 'resource_id', 'id', 'id')
            ->where('resources.resourceable_type', Research::class)
            ->ofType('first_supervisor');
    }
    public function secondSupervisor()
    {
        return $this->hasOneThrough(Curator::class, Resource::class, 'resourceable_id', 'resource_id', 'id', 'id')
            ->where('resources.resourceable_type', Research::class)
            ->ofType('second_supervisor');
    }
    public function thirdSupervisor()
    {
        return $this->hasOneThrough(Curator::class, Resource::class, 'resourceable_id', 'resource_id', 'id', 'id')
            ->where('resources.resourceable_type', Research::class)
            ->ofType('third_supervisor');
    }
    public function discussionCommittee()
    {
        return $this->hasManyThrough(Curator::class, Resource::class, 'resourceable_id', 'resource_id', 'id', 'id')
            ->where('resources.resourceable_type', Research::class)
            ->ofType('discussion_committee');
    }

    //boot

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($research) {
            $research->resource()->delete();
        });
       
        static::updated(function ($research) {
            $research->resource->searchable();
        });
        
    }
}
