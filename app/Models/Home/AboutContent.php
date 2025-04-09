<?php

namespace App\Models\Home;

use App\Models\Resource\Resource;
use App\Traits\FilePath;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;


class AboutContent extends Model
{
    use HasFactory;


    protected $table = 'about_contact';

    protected $fillable = [
        
        'title_en',
        'title_ku',
        'title_ar',
        'description_ku',
        'description_ar',
        'description_en',
        'location_title_en',
        'location_title_ar',
        'location_title_ku',
        'location_description_en',
        'location_description_ar',
        'location_description_ku',
        'coordinates',

    ];



}
