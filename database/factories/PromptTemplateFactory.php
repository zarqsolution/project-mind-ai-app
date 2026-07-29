<?php

namespace Database\Factories;

use App\Models\PromptTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PromptTemplate>
 */
class PromptTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'prompt' => fake()->paragraph(),
            'is_default' => false,
        ];
    }

    /**
     * Mark the template as the default.
     */
    public function default(): static
    {
        return $this->state(['is_default' => true]);
    }
}
