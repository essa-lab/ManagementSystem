<?php

namespace App\Models\Book;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class PrintCondition extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'title_en',
        'title_ku',
        'title_ar',
    ];

    public function printBook()
    {
        return $this->belongsToMany(PrintBook::class, 'print_conditions_pivot');
    }
}
