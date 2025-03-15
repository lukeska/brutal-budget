<?php

use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Inertia\Testing\AssertableInertia;

pest()->group('brutal');

test('a user can create an expense', function () {
    $user = $this->signIn();

    $this->get('/expenses')->assertStatus(200);

    $attributes = Expense::factory()->recycle($user)->raw([
        'notes' => 'My notes',
        'amount' => 2050,
    ]);

    $this->followingRedirects()
        ->put(route('expenses.create'), $attributes)
        ->assertOk()
        ->assertInertia(fn(AssertableInertia $page) => $page
            ->component('Expenses/Index')
            ->where('expenses.0.notes', 'My notes')
            ->where('expenses.0.amount', 2050)
        );
});

test('a user can create monthly expenses from a single amount', function () {
    $this->travelTo(Carbon::create(2024, 1, 31));

    $user = $this->signIn();

    $this->get('/expenses')->assertStatus(200);

    $attributes = Expense::factory()->raw([
        'user_id' => $user->id,
        'notes' => 'My notes',
        'amount' => 10000,
        'months' => 12,
        'date' => Carbon::create(2024, 1, 31),
    ]);

    $this->followingRedirects()
        ->put(route('expenses.create'), $attributes)
        ->assertOk()
        ->assertInertia(function (AssertableInertia $page) {
            return $page
                ->component('Expenses/Index')
                ->where('expenses', function (Collection $expenses) {
                    // Loop through each generated expense and check the date.
                    $expenses->each(function ($expense, $index) {
                        $this->assertEquals(
                            Carbon::create(2024, 1, 31)->addMonthsWithoutOverflow($index)->format('d-m-Y'),
                            $expense['date']
                        );
                    });
                    return true;
                });
        });

    $this->assertEquals(1000000, Expense::sum('amount'));
});

test('a user can edit an expense', function () {
    $user = $this->signIn();

    // The expense creation doesn't go through Laravel Data casts,
    // so the amount stored is 1000, which is displayed as 10 on the frontend.
    $expense = Expense::factory()->recycle($user)->create([
        'amount' => 1000,
    ]);

    $this->get(route('expenses.index'))
        ->assertOk()
        ->assertInertia(function (AssertableInertia $page) {
            return $page
                ->component('Expenses/Index')
                ->where('expenses.0.amount', 10);
        });

    // This amount will go through Laravel data casts.
    // Will be stored as 2000 in the database and displayed as 20 on the frontend.
    $expense->amount = 20;

    $this->followingRedirects()
        ->patch(route('expenses.update', $expense), $expense->toArray())
        ->assertOk()
        ->assertInertia(function (AssertableInertia $page) {
            return $page
                ->component('Expenses/Index')
                ->where('expenses.0.amount', 20);
        });

    $this->assertEquals(2000, $expense->fresh()->amount);
});

test('a user can see expenses for a single team at the time', function () {
    $user = $this->signIn();

    $team1 = $user->currentTeam;

    $team2 = $user->ownedTeams()->create([
        'name' => 'Team 2',
        'personal_team' => false,
    ]);

    Expense::factory()->create([
        'amount' => 111100,
        'user_id' => $user->id,
        'team_id' => $user->currentTeam->id,
    ]);

    $this->get(route('expenses.index'))
        ->assertOk()
        ->assertInertia(function (AssertableInertia $page) {
            return $page
                ->component('Expenses/Index')
                ->has('expenses', 1)
                ->where('expenses.0.amount', 1111);
        });

    $user->switchTeam($team2);

    $this->get(route('expenses.index'))
        ->assertOk()
        ->assertInertia(function (AssertableInertia $page) {
            return $page
                ->component('Expenses/Index')
                ->has('expenses', 0);
        });

    Expense::factory()->create([
        'amount' => 222200,
        'user_id' => $user->id,
        'team_id' => $user->currentTeam->id,
    ]);

    $this->get(route('expenses.index'))
        ->assertOk()
        ->assertInertia(function (AssertableInertia $page) {
            return $page
                ->component('Expenses/Index')
                ->has('expenses', 1)
                ->where('expenses.0.amount', 2222);
        });
});

test('a user can see expenses created by another user within the same team', function () {
    $luca = $this->signIn();

    $viola = User::factory()->withPersonalTeam()->create();

    $team = $viola->currentTeam;

    Expense::factory()->create([
        'amount' => 1111,
        'user_id' => $viola->id,
        'team_id' => $viola->currentTeam->id,
    ]);

    $this->get(route('expenses.index'))
        ->assertOk()
        ->assertInertia(fn(AssertableInertia $page) => $page
            ->component('Expenses/Index')
            ->has('expenses', 0)
        );

    $team->users()->attach($luca, ['role' => 'admin']);

    $luca->refresh();
    $luca->switchTeam($team);

    $this->get(route('expenses.index'))
        ->assertOk()
        ->assertInertia(fn(AssertableInertia $page) => $page
            ->component('Expenses/Index')
            ->has('expenses', 1)
        );
});

test('the main expenses page shows expenses for the current month only', function () {
    $user = $this->signIn();

    Expense::factory()->create([
        'amount' => 222200,
        'user_id' => $user->id,
        'team_id' => $user->currentTeam->id,
        'date' => Carbon::now()->addMonths(-1),
    ]);

    Expense::factory()->create([
        'amount' => 111100,
        'user_id' => $user->id,
        'team_id' => $user->currentTeam->id,
    ]);

    $this->get(route('expenses.index'))
        ->assertOk()
        ->assertInertia(fn(AssertableInertia $page) => $page
            ->component('Expenses/Index')
            ->has('expenses', 1)
            ->where('expenses.0.amount', 1111)
        );
});

test('the main expenses page shows monthly total', function () {
    $user = $this->signIn();

    Expense::factory(10)->create([
        'amount' => 500,
        'user_id' => $user->id,
        'team_id' => $user->currentTeam->id,
        'date' => Carbon::create(2025, 1, 1),
    ]);

    Expense::factory(10)->create([
        'amount' => 1000,
        'user_id' => $user->id,
        'team_id' => $user->currentTeam->id,
        'date' => Carbon::create(2025, 2, 1),
    ]);

    $this->get(route('expenses.index', ['year' => '2025', 'month' => '02']))
        ->assertOk()
        ->assertInertia(fn(AssertableInertia $page) => $page
            ->component('Expenses/Index')
            ->has('expenses', 10)
            ->has('monthlyTotals', 5)
            ->where('monthlyTotals.2.total', 100)
            ->where('monthlyTotals.1.total', 50)
        );
});

test('a team cannot have more than x expenses per month', function () {
    Config::set('global.limits.expenses_per_month', 1);

    $user = $this->signIn();

    Expense::factory()
        ->recycle($user)
        ->count(config('global.limits.expenses_per_month') + 1)
        ->create();

    $attributes = Expense::factory()->raw();

    $this->followingRedirects()
        ->put(route('expenses.create'), $attributes)
        ->assertOk()
        ->assertInertia(fn(AssertableInertia $page) => $page
            ->has('errors')
            ->where('errors.date', 'Too many expenses logged on this team this month.')
        );
});

test('expense date must be within a valid date range', function () {
    $this->travelTo('2024-01-01');

    $user = $this->signIn();

    // Test error messages on create
    $attributes = Expense::factory()->recycle($user)->raw([
        'date' => now()->addYears(2),
    ]);

    $this->followingRedirects()
        ->put(route('expenses.create'), $attributes)
        ->assertOk()
        ->assertInertia(fn(AssertableInertia $page) => $page
            ->has('errors')
            ->where('errors.date', 'The date cannot be more than 2 years in the future.')
        );

    $attributes = Expense::factory()->recycle($user)->raw([
        'date' => now()->subYear(),
    ]);

    $this->followingRedirects()
        ->put(route('expenses.create'), $attributes)
        ->assertOk()
        ->assertInertia(fn(AssertableInertia $page) => $page
            ->has('errors')
            ->where('errors.date', 'The date cannot be more than 1 year in the past.')
        );

    // Test error messages on update
    $expense = Expense::factory()->recycle($user)->create([
        'amount' => 1000,
    ]);

    $expense->amount = 2000;
    $expense->date = now()->addYears(2);

    $this->followingRedirects()
        ->patch(route('expenses.update', $expense), $expense->toArray())
        ->assertOk()
        ->assertInertia(fn(AssertableInertia $page) => $page
            ->has('errors')
            ->where('errors.date', 'The date cannot be more than 2 years in the future.')
        );

    $expense->amount = 2000;
    $expense->date = now()->subYear();

    $this->followingRedirects()
        ->patch(route('expenses.update', $expense), $expense->toArray())
        ->assertOk()
        ->assertInertia(fn(AssertableInertia $page) => $page
            ->has('errors')
            ->where('errors.date', 'The date cannot be more than 1 year in the past.')
        );
});
