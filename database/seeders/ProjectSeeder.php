<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $luca = User::find(1);

        Project::factory()->create([
            'name' => 'Summer vacation '.Carbon::now()->addYear(1)->year,
            'team_id' => $luca->currentTeam->id,
            'hex' => '#ef4444',
        ]);

        Project::factory()->create([
            'name' => 'Kitchen renewal',
            'team_id' => $luca->currentTeam->id,
            'hex' => '#84cc16',
        ]);

        Project::factory()->create([
            'name' => 'Start snowboarding',
            'team_id' => $luca->currentTeam->id,
            'hex' => '#6366f1',
        ]);
    }
}
