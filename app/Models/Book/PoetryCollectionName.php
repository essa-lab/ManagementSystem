<?php

namespace App\Models\Book;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoetryCollectionName extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'name_ar',
        'name_en',
        'name_ku',
        'book_id',
        'poetry_collection_id'
    ];
    public function poetryCollection(){
        return $this->belongsTo(PoetryCollection::class,'poetry_collection_id');
    }
    public function book(){
        return $this->belongsTo(Book::class,'book_id');
    }
}
