<?php

namespace App\Models\Book;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookSpecificSubject extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'title_ar',
        'title_en',
        'title_ku',
        'book_id'
    ];

    public function book(){
        return $this->belongsTo(Book::class,'book_id');
    }

}
