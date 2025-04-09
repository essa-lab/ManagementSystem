<?php

namespace Database\Factories\Article;

use App\Models\Article\ArticleType;
use App\Models\Article\ScientificBranches;
use App\Models\Article\Specification;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article\Article>
 */
class ArticleFactory extends Factory
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
            'subtitle_en' => $this->faker->sentence,
            'subtitle_ku' => $this->faker->sentence,
            'subtitle_ar' => $this->faker->sentence,
            'secondary_title_en' => $this->faker->sentence,
            'secondary_title_ku' => $this->faker->sentence,
            'secondary_title_ar' => $this->faker->sentence,
            'publication_date' => $this->faker->date(),
            'research_year' => $this->faker->year,
            'duration_of_research' => $this->faker->numberBetween(1, 10) . ' years',
            'course' => $this->faker->word,
            'number' => $this->faker->randomNumber(3),
            'number_of_pages' => $this->faker->numberBetween(10, 500),
            'map' => $this->faker->boolean ,
            'place_of_printing_en'=>$this->faker->city,
        'place_of_printing_ar'=>$this->faker->city,
        'place_of_printing_ku'=>$this->faker->city,
            'journal_name' => $this->faker->company,
            'journal_volume' => $this->faker->randomDigitNotNull(),
            'article_scientific_classification_id' => ScientificBranches::factory()->create()->id,
            'article_type_id' =>ArticleType::factory()->create()->id,
            'article_specification_id' => Specification::factory()->create()->id,
        ];
    }
}
