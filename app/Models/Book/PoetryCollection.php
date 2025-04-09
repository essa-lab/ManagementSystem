<?php

namespace App\Models\Book;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoetryCollection extends BaseModel
{
    use HasFactory;
    protected $with = ['name'];

    protected $fillable = [
        'title_ar',
        'title_en',
        'title_ku'
    ];
    public function name()
    {
        return $this->hasOne(PoetryCollectionName::class);
    }

    
}
