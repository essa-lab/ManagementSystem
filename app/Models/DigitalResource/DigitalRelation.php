<?php

namespace App\Models\DigitalResource;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class DigitalRelation extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'title_en',
        'title_ku',
        'title_ar',
        'digital_resource_id'
    ];

    public function digitalResource(){
        return $this->hasMany(DigitalResource::class);
    }

}
