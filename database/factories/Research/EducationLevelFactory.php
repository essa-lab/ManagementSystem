<?php

namespace Database\Factories\Research;

use Illuminate\Database\Eloquent\Factories\Factory;



/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Research\EducationLevel>
 */
class EducationLevelFactory extends Factory
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
