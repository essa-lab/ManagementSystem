<?php

namespace App\Models\Book;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintBook extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'publisher_ar',
        'publisher_en',
        'publisher_ku',
        'print_house_ar',
        'print_house_en',
        'print_house_ku',
        'print_location_ar',
        'print_location_en',
        'print_location_ku',
        'print_year',
        'year_type',
        'book_id'
    ];
    public function book(){
        return $this->belongsTo(Book::class,'book_id');
    }
    public function conditions()
    {
        return $this->belongsToMany(PrintCondition::class, 'print_conditions_pivot');
    }
    public function type()
    {
        return $this->belongsToMany(PrintType::class, 'print_types_pivot')
        ->withPivot('title_en', 'title_ar','title_ku') 
        ;
    }

    public function add($data, $bookId){
        $data['book_id']=$bookId;
        $printBook = $this->create($data);

        if (!empty($data['conditions'])) {
            $printBook->conditions()->sync($data['conditions']);
        }
        
        if (!empty($data['types'])) {
            $pivotData = collect($data['types'])->mapWithKeys(fn($type) => [
                $type['id'] => [
                    'title_en' => $type['title_en'] ?? null,
                    'title_ar' => $type['title_ar'] ?? null,
                    'title_ku' => $type['title_ku'] ?? null,
                ]
            ])->toArray();
    
            $printBook->type()->syncWithoutDetaching($pivotData);
        }
    }

    public function editSelfAndRelation($data, $bookInformation){
        $conditions = $data['conditions']??null;
        $types = $data['types']??null;
        unset($data['conditions']);
        unset($data['types']);

        $bookInformation->update($data);
        
        if (!empty($conditions)) {
            $bookInformation->conditions()->detach();
            $bookInformation->conditions()->sync($conditions);
                        // $bookInformation->conditions()->sync($conditions);
            info('done updating');

        }

        if (!empty($types)) {
            $pivotData = collect($types)->mapWithKeys(fn($type) => [
                $type['id'] => [
                    'title_en' => $type['title_en'] ?? null,
                    'title_ar' => $type['title_ar'] ?? null,
                    'title_ku' => $type['title_ku'] ?? null,
                ]
            ])->toArray();
   
            $bookInformation->type()->syncWithoutDetaching($pivotData);
        }
    }

}
