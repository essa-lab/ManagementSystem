<?php

namespace App\Models\Resource;

use App\Models\BaseModel;
use App\Traits\FilePath;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Media extends BaseModel
{
    use HasFactory,FilePath;
    protected $table="medias";
    protected $fillable = [
        'type',
        'path',
        'resource_id'
    ];

    public function resource(){
        return $this->belongsTo(Resource::class,'resource_id','id');
    }

    public function getFilePathAttribute()
    {
       return  $this->filePath('path');
    }

    protected static function boot()
    {
        parent::boot();

       


        static::created(function ($media) {
            $media->resource->searchable();
        });
    }

}
