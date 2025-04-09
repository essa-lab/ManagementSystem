<?php

namespace App\Models\Dashboard;

use App\Models\BaseModel;
use App\Models\Resource\Resource;
use App\Traits\FilePath;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;


class Navigation extends BaseModel
{
    use HasFactory;
    protected $table = 'naviagation';
    protected $fillable = [
        
        'title_en',
        'title_ar',
        'title_ku',
        'url',
    ];

}
