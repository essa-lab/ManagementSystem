<?php

namespace App\Models\Book;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class PrintType extends BaseModel
{
    use HasFactory;
    // protected $with = ['printBook'];
    protected $fillable = [
        'title_en',
        'title_ku',
        'title_ar',
    ];

    public function printBook()
    {
        return $this->belongsToMany(PrintBook::class, 'print_type_pivot')
        ->withPivot('title_en', 'title_ar','title_ku') ;
    }

}
