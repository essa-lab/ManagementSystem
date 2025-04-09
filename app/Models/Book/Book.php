<?php

namespace App\Models\Book;

use App\Models\BaseModel;
use App\Models\Resource\Curator;
use App\Models\Resource\Resource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Book extends BaseModel
{
    use SoftDeletes, HasFactory;
    protected $fillable = [
        'registeration_number',
        'order_number',
        'subtitle_ar',
        'subtitle_ku',
        'subtitle_en',

        'translated_title_ar',
        'translated_title_ku',
        'translated_title_en',

        'location_of_congress',
        'dewey_decimal_classification',
        'number_of_pages',
        'volume_number',
        'volume',
        'print_circulation',
        'department',
        'table_of_content_condition',
        'cover_specification',
        'book_national_id_number',
        'publishing_house_en',
        'publishing_house_ar',
        'publishing_house_ku',

        'price',
        'barcode',
        'isbn',
        'editor'
    ];

    public function resource()
    {
        return $this->morphOne(Resource::class, 'resourceable');
    }
    public function bookTranslator()
    {
        return $this->hasMany(BookTranslator::class);
    }
//     public function bookTranslateType()
// {
//     return $this->hasOneThrough(
//         TranslationType::class,
//         BookTranslator::class,
//         'book_id',         
//         'id',            
//         'id',             
//         'translate_type_id' 
//     );
// }

    public function specificSubjects()
    {
        return $this->hasMany(BookSpecificSubject::class);
    }
    public function poetryCollectionName()
    {
        return $this->hasOne(PoetryCollectionName::class);
    }
 
    public function printInformation()
    {
        return $this->hasOne(PrintBook::class);
    }
    public function authors()
    {
        return $this->hasManyThrough(Curator::class, Resource::class, 'resourceable_id', 'resource_id', 'id', 'id')
            ->where('resources.resourceable_type', Book::class)
            ->ofType('author');
    }
    //boot

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($book) {
            $book->resource()->delete();
        });

        
        static::updated(function ($book) {
            info('test');
            $book->resource->searchable();
        });


    }

    public function addPrintInformation($data)
    {
        // (new PrintBook())->add($data,$this->id);

        $printInfo = $this->printInformation;
            $printInfo==null?
            (new PrintBook())->add($data, $this->id) :
            $printInfo->editSelfAndRelation($data, $printInfo);
    }

}
