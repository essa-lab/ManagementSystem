<?php

namespace App\Models;

use App\Models\Resource\Resource;
use App\Traits\FilePath;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;


class Library extends BaseModel
{
    use HasFactory,FilePath;

    protected $fillable = [
        
        'name_en',
        'name_ku',
        'name_ar',
        'logo',
        'location'
    ];




    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function resources()
    {
        return $this->hasMany(Resource::class);
    }
    // protected function logoPath(): Attribute
    // {
    //     return new Attribute(
    //         get: fn() => $this->filePath('logo'),
    //     );
    // }
    public function getLogoPathAttribute()
    {
       return  $this->filePath('logo');
    }

}
