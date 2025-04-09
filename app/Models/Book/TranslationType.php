<?php

namespace App\Models\Book;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class TranslationType extends BaseModel
{
    use HasFactory;
    protected $table = 'translate_types';
    
    protected $fillable = [
        'title_ar',
        'title_en',
        'title_ku',
    ];
    public function bookTranslator()
    {
        return $this->hasMany(BookTranslator::class,'translate_type_id');
    }
}
