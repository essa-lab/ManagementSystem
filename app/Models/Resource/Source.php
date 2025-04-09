<?php

namespace App\Models\Resource;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Source extends BaseModel
{
    use HasFactory;
    // protected $with = ['resourceSource'];
    protected $fillable = [
        'title_ar',
        'title_en',
        'title_ku'
    ];
    public function resourceSource()
    {
        return $this->hasOne(ResourceSource::class);
    }


}
