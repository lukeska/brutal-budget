<?php

use Illuminate\Support\Facades\Config;
use function Pest\Laravel\post;

pest()->group('brutal');

test('a user cannot own more than x teams', function () {
    Config::set('global.limits.owned_teams_per_user', 1);

    $this->signIn();

    $this->post('/teams', [
        'name' => 'Test Team',
    ])->assertStatus(403);
});
