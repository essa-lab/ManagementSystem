<?php

namespace Database\Factories\DigitalResource;

use App\Http\Resources\DigitalResource\DigitalTypeResource;
use App\Models\DigitalResource\DigitalFormat;
use App\Models\DigitalResource\DigitalResourceType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DigitalResource\DigitalResource
 */
class DigitalResourceFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'digital_format_id' => DigitalFormat::factory()->create()->id,
            'digital_resource_type_id' => DigitalResourceType::factory()->create()->id,
            'identifier' => $this->faker->uuid, 
            'publisher' => $this->faker->company,
            'coverage' => $this->faker->sentence, 
        ];
    }
}
