<?php

namespace App\Models\Dashboard;

use App\Models\BaseModel;
use App\Models\Resource\Resource;
use App\Traits\FilePath;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;


class QuickLinks extends BaseModel
{
    use HasFactory;
    protected $table = 'quick_links_footer';

    protected $fillable = [
        
        'title_en',
        'title_ar',
        'title_ku',
        'link',
    ];

}
