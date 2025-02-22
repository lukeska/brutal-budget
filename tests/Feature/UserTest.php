<?php

use App\Models\User;

pest()->group('brutal');

test('it generates onboarding statuses when a user is created', function () {
    $user = User::factory()->create();

    expect($user->onboardingStatuses)->toHaveCount(3);
});
