<?php

namespace Database\Factories\Book;

use App\Models\Book;
use App\Models\BookSource;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Library;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book\Book>
 */
class BookFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'registeration_number' => Str::uuid(),
        'order_number' => $this->faker->unique()->randomNumber(8),
        'subtitle_ar' => $this->faker->sentence,
        'subtitle_ku' => $this->faker->sentence,
        'subtitle_en' => $this->faker->sentence,
        'translated_title_ar' => $this->faker->sentence,
        'translated_title_ku' => $this->faker->sentence,
        'translated_title_en' => $this->faker->sentence,
        'location_of_congress' => $this->faker->city,
        'dewey_decimal_classification' => $this->faker->randomNumber(4) . '.' . $this->faker->randomNumber(2),
        'number_of_pages' => $this->faker->numberBetween(50, 1000),
        'volume_number' => $this->faker->randomDigitNotNull(),
        'volume' => $this->faker->randomDigitNotNull(),
        'print_circulation' => $this->faker->randomNumber(3),
        'department' => $this->faker->word,
        'table_of_content_condition' => $this->faker->sentence,
        'cover_specification' => $this->faker->sentence,
        'book_national_id_number' => $this->faker->unique()->isbn13(),
        'publishing_house_en' => $this->faker->company,
        'publishing_house_ar' => $this->faker->company,
        'publishing_house_ku' => $this->faker->company,
        'price' => $this->faker->randomFloat(2, 10, 500),
        'barcode' => Str::random(12),
        'isbn' => $this->faker->isbn13(),
        'editor' => $this->faker->name,
    ];
    }
}
