<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\Project;
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

        foreach (range(-4, 1) as $month) {
            foreach (range(1, 40) as $i) {
                Expense::factory()->create([
                    'user_id' => $luca->id,
                    'date' => Carbon::now()->addMonths($month)->setDay(rand(1, 28)),
                ]);
            }
        }

        // project expenses
        foreach (Project::all() as $project) {
            foreach (range(1, 5) as $i) {
                Expense::factory()
                    ->recycle($project)
                    ->create([
                        'user_id' => $luca->id,
                        'date' => Carbon::now()->setDay(rand(1, 28)),
                    ]);

                Expense::factory()
                    ->recycle($project)
                    ->create([
                        'user_id' => $luca->id,
                        'project_id' => $project->id,
                        'date' => Carbon::now()->subMonth()->setDay(rand(1, 28)),
                    ]);
            }
        }
    }
}
