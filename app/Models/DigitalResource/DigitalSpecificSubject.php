<?php

namespace App\Models\DigitalResource;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class DigitalSpecificSubject extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'title_en',
        'title_ku',
        'title_ar',
        'digital_resource_id'
    ];

    public function digitalResource(){
        return $this->belongsTo(DigitalResource::class,'digital_resource_id');
    }

}
