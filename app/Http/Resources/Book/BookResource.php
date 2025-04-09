<?php

namespace App\Http\Resources\Book;

use App\Http\Resources\Resaerch\ResearchFormatResource;
use App\Http\Resources\ResearchTypeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [


            'id' => $this->id,
            'registeration_number' => $this->registeration_number,
            'order_number' => $this->order_number,

            'location_of_congress' => $this->location_of_congress,
            'dewey_decimal_classification' => $this->dewey_decimal_classification,
            'number_of_pages' => $this->number_of_pages,
            'volume_number' => $this->volume_number,
            'volume' => $this->volume,
            'print_circulation' => $this->print_circulation,
            'department' => $this->department,
            'table_of_content_condition' => $this->table_of_content_condition,
            'cover_specification' => $this->cover_specification,
            'book_national_id_number' => $this->book_national_id_number,

           'publishing_house_ar' => $this->publishing_house_ar,
            'publishing_house_en' => $this->publishing_house_en,
            'publishing_house_ku' => $this->publishing_house_ku,
            'publishing_house' => $this->{'publishing_house_' . app()->getLocale()},

            'subtitle_ar' => $this->subtitle_ar,
            'subtitle_en' => $this->subtitle_en,
            'subtitle_ku' => $this->subtitle_ku,
            'subtitle' => $this->{'subtitle_' . app()->getLocale()},


            'translated_title_ar' => $this->translated_title_ar,
            'translated_title_en' => $this->translated_title_en,
            'translated_title_ku' => $this->translated_title_ku,
            'translated_title' => $this->{'translated_title_' . app()->getLocale()},


            'price' => $this->price,
            'barcode' => $this->barcode,
            'isbn' => $this->isbn,
            'editor' => $this->editor,
            'poetry_collection_name'=>new PoetryCollectionNameResource($this->whenLoaded('poetryCollectionName')),
            // 'poetry_collection_name'=>new PoetryCollectionNameResource($this->whenLoaded('poetryCollectionName')),

            // 'translator_type'=>new TranslationTypeResource($this->whenLoaded('bookTranslateType')),
            'book_translator'=>BookTranslatorResource::collection($this->whenLoaded('bookTranslator')),

            'specific_subject'=>BookSpecificSubjectResource::collection($this->whenLoaded('specificSubjects')),
            'print_information'=>new BookPrintInformationResource($this->whenLoaded('printInformation'))

        ];
    }
}
