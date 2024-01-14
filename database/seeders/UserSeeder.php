<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $luca = User::factory()->withPersonalTeam()->create([
            'name' => 'luca',
            'email' => 'luca@example.com',
            'password' => Hash::make('123123123'),
        ]);

        $viola = User::factory()->withPersonalTeam()->create([
            'name' => 'viola',
            'email' => 'viola@example.com',
            'password' => Hash::make('123123123'),
        ]);

        $luca->currentTeam->users()->attach($viola, ['role' => 'admin']);
    }
}
