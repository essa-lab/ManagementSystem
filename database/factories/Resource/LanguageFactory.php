<?php

namespace Database\Factories\Resource;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Resource\Language>
 */
class LanguageFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $languageCode = $this->faker->languageCode();
        return [
            'title_ar' => $languageCode,
            'title_en' => $languageCode,
            'title_ku' => $languageCode,

        ];
    }
}
