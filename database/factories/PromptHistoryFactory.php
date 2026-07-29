<?php

namespace Database\Factories;

use App\Models\PromptHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PromptHistory>
 */
class PromptHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'job_description' => fake()->paragraph(),
            'used_prompt' => fake()->paragraph(),
            'generated_proposal' => fake()->paragraphs(2, true),
        ];
    }
}
