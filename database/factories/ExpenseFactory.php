<?php

namespace Database\Factories;

use App\Models\Category;
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
        return [
            'date' => now(),
            'amount' => $this->faker->numberBetween(0, 100) * 100,
            'user_id' => User::factory()->withPersonalTeam(),
            'team_id' => function (array $attributes) {
                return User::find($attributes['user_id'])->currentTeam->id;
            },
            'category_id' => function (array $attributes) {
                return User::find($attributes['user_id'])->currentTeam->categories->random()->id;
            },
            'notes' => function (array $attributes) {
                return $this->faker->expenseNote(Category::find($attributes['category_id'])->name);
            },
            'is_regular' => $this->faker->boolean(80),
            'project_id' => null,
        ];
    }

    public function isRegular(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_regular' => true,
            ];
        });
    }

    public function isNotRegular(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_regular' => false,
            ];
        });
    }
}
