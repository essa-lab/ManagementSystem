<?php

namespace Database\Factories\Research;

use App\Models\Research\EducationLevel;
use App\Models\Research\ResearchFormat;
use App\Models\Research\ResearchType;
use Illuminate\Database\Eloquent\Factories\Factory;



/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Research\Research>
 */
class ResearchFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'registeration_number' => $this->faker->unique()->randomNumber(8),
            'order_number' => $this->faker->randomNumber(5),
            'publish_date' => $this->faker->date(),
            'discussion_date' => $this->faker->optional()->date(),
            'number_of_pages' => $this->faker->numberBetween(50, 500),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'classification' => $this->faker->word,
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'education_level_id' => EducationLevel::factory()->create()->id,
            'research_type_id' => ResearchType::factory()->create()->id,
            'research_format_id' => ResearchFormat::factory()->create()->id,
            'university_en' => $this->faker->company,
            'university_ar' => $this->faker->company,
            'university_ku' => $this->faker->company,
            'college_en' => $this->faker->word,
            'college_ku' => $this->faker->word,
            'college_ar' => $this->faker->word,
            'education_major_en' => $this->faker->word,
            'education_major_ku' => $this->faker->word,
            'education_major_ar' => $this->faker->word,
        ];
    }
}
