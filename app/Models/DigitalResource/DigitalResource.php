<?php

namespace App\Models\DigitalResource;

use App\Models\BaseModel;
use App\Models\Resource\Curator;
use App\Models\Resource\Resource;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class DigitalResource extends BaseModel
{
    use HasFactory;
    protected $fillable = [

        'digital_format_id',
        'digital_resource_type_id',
        'identifier',
        'publisher',
        'coverage'
    ];

    public function resource()
    {
        return $this->morphOne(Resource::class, 'resourceable');
    }
    public function digitalType()
    {
        return $this->belongsTo(DigitalResourceType::class, 'digital_resource_type_id');
    }
    public function digitalFormat()
    {
        return $this->belongsTo(DigitalResourceType::class, 'digital_format_id');
    }
    public function relations()
    {
        return $this->hasMany(DigitalRelation::class);
    }
    public function right()
    {
        return $this->hasOne(DigitalRight::class);
    }
    public function specificSubject()
    {
        return $this->hasOne(DigitalSpecificSubject::class);
    }
    public function creators()
    {
        return $this->hasManyThrough(Curator::class, Resource::class, 'resourceable_id', 'resource_id', 'id', 'id')
            ->where('resources.resourceable_type', DigitalResource::class)
            ->ofType('creator');
    }
    public function contributers()
    {
        return $this->hasManyThrough(Curator::class, Resource::class, 'resourceable_id', 'resource_id', 'id', 'id')
            ->where('resources.resourceable_type', DigitalResource::class)
            ->ofType('contributer');
    }

    //boot

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($digitalResource) {
            $digitalResource->resource()->delete();
        });

        
        static::updated(function ($digitalResource) {
            $digitalResource->resource->searchable();
        });
    }


}
