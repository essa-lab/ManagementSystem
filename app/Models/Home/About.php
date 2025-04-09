<?php

namespace App\Models\Home;

use App\Models\Resource\Resource;
use App\Traits\FilePath;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;


class About extends Model
{
    use HasFactory,FilePath;

    protected $table = 'about_banner';

    protected $fillable = [
        
        'title_en',
        'title_ku',
        'title_ar',
        'content_en',
        'content_ku',
        'content_ar',
        'image',
    ];

    public function getImagePathAttribute()
    {
       return  $this->filePath('image');
    }


}
