<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->withPersonalTeam()->create();

        return [
            'date' => now(),
            'amount' => $this->faker->numberBetween(0, 100) * 100,
            'notes' => $this->faker->sentence(6),
            'user_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'category_id' => $user->currentTeam->categories->first()->id,
        ];
    }
}
