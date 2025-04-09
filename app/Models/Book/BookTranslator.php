<?php

namespace App\Models\Book;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookTranslator extends BaseModel
{
    use HasFactory;
    protected $table = "book_translator";
    protected $fillable = [
        'name_ar',
        'name_en',
        'name_ku',
        'book_id',
        'translate_type_id'
    ];

    public function type(){
        return $this->belongsTo(TranslationType::class,'translate_type_id');
    }
    public function book(){
        return $this->belongsTo(Book::class,'book_id');
    }

}
