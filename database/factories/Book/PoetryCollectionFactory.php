<?php

namespace Database\Factories\Book;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book\PoetryCollection
 */
class PoetryCollectionFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title_ar' => $this->faker->firstName,
            'title_en' => $this->faker->firstName,
            'title_ku' =>$this->faker->firstName,

        ];
    }
}
