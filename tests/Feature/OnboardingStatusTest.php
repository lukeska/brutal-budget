<?php

use App\Models\Expense;
use App\Models\Project;
use Illuminate\Support\Facades\Mail;

pest()->group('brutal');

test('it updates the status when first expense is created', function () {
    $user = $this->signIn();

    expect($user->onboardingStatusExpenseCreated->done())->toBeFalse();

    $attributes = Expense::factory()->recycle($user)->raw();

    $this->put(route('expenses.create'), $attributes);

    expect($user->onboardingStatusExpenseCreated->fresh()->done())->toBeTrue();
});

test('it updates the status when first project is created', function () {
    $user = $this->signIn();

    expect($user->onboardingStatusProjectCreated->done())->toBeFalse();

    $attributes = Project::factory()->recycle($user->currentTeam)->raw();

    $this->put(route('projects.create'), $attributes);

    expect($user->onboardingStatusProjectCreated->fresh()->done())->toBeTrue();
});

test('it updates the status when team member is invited', function () {
    Mail::fake();

    $user = $this->signIn();

    expect($user->onboardingStatusTeamMemberInvited->done())->toBeFalse();

    $this->post('/teams/' . $user->currentTeam->id . '/members', [
        'email' => 'test@example.com',
        'role' => 'admin',
    ]);

    expect($user->onboardingStatusTeamMemberInvited->fresh()->done())->toBeTrue();
});
