<?php

namespace App\Models\Resource;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ResourceSetting extends BaseModel
{
    use HasFactory;

    protected $table="resource_settings";
    protected $fillable = [
        'resource_id',
        'availability',
        'max_allowed_day',
        'allow_renewal',
        'renewal_cycle',
        'locked'
    ];

    public function resource(){
        return $this->belongsTo(Resource::class,'resource_id');
    }

}
