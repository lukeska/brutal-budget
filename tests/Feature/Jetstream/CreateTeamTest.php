<?php

namespace Tests\Feature\Jetstream;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_teams_can_be_created(): void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $response = $this->post('/teams', [
            'name' => 'Test Team',
        ]);

        $this->assertCount(2, $user->fresh()->ownedTeams);
        /** @phpstan-ignore-next-line  */
        $this->assertEquals('Test Team', $user->fresh()->ownedTeams()->latest('id')->first()->name);
    }
}
