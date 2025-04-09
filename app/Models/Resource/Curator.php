<?php

namespace App\Models\Resource;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Curator extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'name_en',
        'name_ku',
        'name_ar',
        'type',
        'resource_id'
    ];

    public function resource(){
        return $this->belongsTo(Resource::class,'resource_id','id');
    }

    public function education(){
        return $this->hasOne(CuratorEducation::class);
    }
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($curator) {
            $curator->resource->searchable();
        });
    }





}
