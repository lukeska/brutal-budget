<?php

use App\Models\Project;
use Illuminate\Support\Facades\Config;
use Inertia\Testing\AssertableInertia;
use function Pest\Laravel\get;
use function Pest\Laravel\followingRedirects;

pest()->group('brutal');

test('a team cannot have more than x projects', function () {
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
        ->assertInertia(fn(AssertableInertia $page) => $page
            ->has('errors')
            ->where('errors.name', 'You reach the limit of projects this team can have.')
        );
});
