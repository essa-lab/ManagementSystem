<?php

namespace Database\Factories\Resource;

use App\Models\Article\Article;
use App\Models\Book\Book;
use App\Models\DigitalResource\DigitalResource;
use App\Models\Library;
use App\Models\Research\Research;
use App\Models\Resource\Language;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Resource\Resource>
 */
class ResourceFactory extends Factory
{

    public function definition()
    {
        // Randomly select a resource type
        $resourceableTypes = [
            Book::class,
            Article::class,
            Research::class,
            DigitalResource::class
        ];
        $resourceableType = $this->faker->randomElement($resourceableTypes);

        // Create an instance of the selected model and get its ID
        $resourceableId = $resourceableType::factory()->create()->id;

        return [
            'library_id' => Library::inRandomOrder()->first()->id,
            'language_id' => Language::inRandomOrder()->first()->id,
            'uuid' => Str::uuid(),
            'link' => $this->faker->url,
            'registry_date' => $this->faker->date(),
            'arrival_date' => $this->faker->date(),
            'number_of_copies' => $this->faker->numberBetween(1, 100),
            'title_en' => $this->faker->sentence,
            'title_ku' => $this->faker->sentence,
            'title_ar' => $this->faker->sentence,
            'resourceable_id' => $resourceableId,
            'resourceable_type' => $resourceableType,
        ];
    }
}
