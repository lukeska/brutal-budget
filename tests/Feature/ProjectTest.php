<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/** @group brutal */
class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_team_cannot_have_more_than_x_projects()
    {
        Config::set('global.limits.projects_per_team', 1);

        $user = $this->signIn();

        Project::factory()
            ->recycle($user->currentTeam)
            ->count(config('global.limits.projects_per_team') + 1)
            ->create();

        $attributes = Project::factory([
            'name' => 'My test project',
        ])->raw();

        $this->get(route('projects.index'));

        $this->followingRedirects()
            ->put(route('projects.create'), $attributes)
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('errors')
                ->where('errors.name', 'You reach the limit of projects this team can have.')
            );
    }
}
