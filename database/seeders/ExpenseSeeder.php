<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $luca = User::find(1);

        foreach (range(1,20) as $i) {
            Expense::factory()->create([
                'user_id' => $luca->id,
                'team_id' => $luca->currentTeam->id,
                'category_id' => rand(1,10),
                'date' => Carbon::create(
                    Carbon::now()->year,
                    Carbon::now()->month,
                    rand(1,28)
                )
            ]);
        }

        foreach (range(1,20) as $i) {
            Expense::factory()->create([
                'user_id' => $luca->id,
                'team_id' => $luca->currentTeam->id,
                'category_id' => rand(1,10),
                'date' => Carbon::create(
                    Carbon::now()->year,
                    Carbon::now()->addMonth(-1)->month,
                    rand(1,28)
                )
            ]);
        }
    }
}
