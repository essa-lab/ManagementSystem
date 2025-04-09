<?php

namespace App\Models\DigitalResource;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitalResourceType extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'title_ar',
        'title_en',
        'title_ku'
    ];

    public function digitalResource(){
        return $this->hasMany(DigitalResource::class);
    }
}
