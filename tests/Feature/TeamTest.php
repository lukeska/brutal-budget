<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/** @group brutal */
class TeamTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_cannot_own_more_than_x_teams()
    {
        Config::set('global.limits.owned_teams_per_user', 1);

        $this->signIn();

        $this->post('/teams', [
            'name' => 'Test Team',
        ])->assertStatus(403);
    }
}
