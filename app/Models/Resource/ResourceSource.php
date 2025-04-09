<?php

namespace App\Models\Resource;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceSource extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'title_ar',
        'title_ku',
        'title_en',
        'resource_id',
        'source_id'
    ];

    public function resource(){
        return $this->belongsTo(Resource::class,'resource_id','id');
    }
    public function source(){
        return $this->belongsTo(Source::class);
    }
    

}
