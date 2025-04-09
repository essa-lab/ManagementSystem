<?php

namespace App\Models\Resource;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Editor extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'content',
        'type',
        'resource_id',
        'language'
    ];

    public function resource(){
        return $this->belongsTo(Resource::class,'resource_id','id');
    }
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

}
