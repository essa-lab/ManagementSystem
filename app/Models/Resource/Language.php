<?php

namespace App\Models\Resource;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Language extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'title_ar',
        'title_en',
        'title_ku'
    ];

}
