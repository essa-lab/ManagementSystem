<?php

namespace App\Models\Home;

use App\Models\Resource\Resource;
use App\Traits\FilePath;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;


class Home extends Model
{
    use HasFactory,FilePath;

    protected $table = 'home_banner';

    protected $fillable = [
        
        'title_en',
        'title_ku',
        'title_ar',
        'subtitle_en',
        'subtitle_ku',
        'subtitle_ar',
        'asset',
    ];

    public function getAssetPathAttribute()
    {
       return  $this->filePath('asset');
    }


}
