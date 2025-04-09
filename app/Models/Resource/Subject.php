<?php

namespace App\Models\Resource;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Subject extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'title_ar',
        'title_en',
        'title_ku'
    ];

    public function subjects()
    {
        return $this->belongsToMany(Resource::class, 'resource_subjects');
    }

    protected static function boot()
    {
        parent::boot();


    }
}
