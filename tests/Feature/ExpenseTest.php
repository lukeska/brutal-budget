<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/** @group brutal */
class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_create_an_expense()
    {
        $user = $this->signIn();

        $this->get('/expenses')->assertStatus(200);

        $attributes = Expense::factory()->recycle($user)->raw([
            'notes' => 'My notes',
            'amount' => 2050,
        ]);

        $this->followingRedirects()
            ->put(route('expenses.create'), $attributes)
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Expenses/Index')
                ->where('expenses.0.notes', 'My notes')
                ->where('expenses.0.amount', 2050)
            );
    }

    /** @test */
    public function a_user_can_create_monthly_expenses_from_a_single_amount()
    {
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
                        $expenses->each(function ($expense, $index) {
                            $this->assertEquals(
                                Carbon::create(2024, 1, 31)->addMonthsWithoutOverflow($index)->format('d-m-Y'),
                                $expense['date']);
                        });

                        return true;
                    });
            }
            );

        $this->assertEquals(1000000, Expense::all()->sum('amount'));
    }

    /** @test */
    public function a_user_can_edit_an_expense()
    {
        $user = $this->signIn();

        // the expense creation doesn't go through Laravel Data casts,
        // so the amount stored is 1000, which is displayed as 10 on the frontend
        $expense = Expense::factory()->recycle($user)->create([
            'amount' => 1000,
        ]);

        $this->get(route('expenses.index'))
            ->assertOk()
            ->assertInertia(function (AssertableInertia $page) {
                return $page
                    ->component('Expenses/Index')
                    ->where('expenses.0.amount', 10);
            }
            );

        // This amount will go through Laravel data casts.
        // Will be stored as 2000 in the database, and displayed as 20 on the frontend
        $expense->amount = 20;

        $this->followingRedirects()
            ->patch(route('expenses.update', $expense), $expense->toArray())
            ->assertOk()
            ->assertInertia(function (AssertableInertia $page) {
                return $page
                    ->component('Expenses/Index')
                    ->where('expenses.0.amount', 20);
            }
            );

        $this->assertEquals(2000, $expense->fresh()->amount);
    }

    /** @test */
    public function a_user_can_see_expenses_for_a_single_team_at_the_time()
    {
        $user = $this->signIn();

        $team1 = $user->currentTeam;

        $team2 = $user->ownedTeams()->create([
            'name' => 'Team 2',
            'personal_team' => false,
        ]);

        $expenseGroup1 = Expense::factory()->create([
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
            }
            );

        $user->switchTeam($team2);

        $this->get(route('expenses.index'))
            ->assertOk()
            ->assertInertia(function (AssertableInertia $page) {
                return $page
                    ->component('Expenses/Index')
                    ->has('expenses', 0);
                //->where('expenses.0.amount', '1111.00')
            }
            );

        $expenseGroup2 = Expense::factory()->create([
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
            }
            );
    }

    /** @test */
    public function a_user_can_see_expenses_created_by_another_user_within_the_same_team()
    {
        /** @var User $luca */
        $luca = $this->signIn();

        $viola = User::factory()->withPersonalTeam()->create();

        /** @var \Laravel\Jetstream\Team $team */
        $team = $viola->currentTeam;

        Expense::factory()->create([
            'amount' => 1111,
            'user_id' => $viola->id,
            'team_id' => $viola->currentTeam->id,
        ]);

        $this->get(route('expenses.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Expenses/Index')
                ->has('expenses', 0)
            );

        $team->users()->attach($luca, ['role' => 'admin']);

        $luca->refresh();
        $luca->switchTeam($team);

        $this
            ->get(route('expenses.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Expenses/Index')
                ->has('expenses', 1)
            );
    }

    /** @test */
    public function the_main_expenses_page_shows_expenses_for_the_current_month_only()
    {
        $user = $this->signIn();

        $expensePastMonth = Expense::factory()->create([
            'amount' => 222200,
            'user_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'date' => Carbon::now()->addMonth(-1),
        ]);

        $expenseCurrentMonth = Expense::factory()->create([
            'amount' => 111100,
            'user_id' => $user->id,
            'team_id' => $user->currentTeam->id,
        ]);

        $this
            ->get(route('expenses.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Expenses/Index')
                ->has('expenses', 1)
                ->where('expenses.0.amount', 1111)
            );
    }

    /** @test */
    public function the_main_expenses_page_shows_monthly_total()
    {
        $user = $this->signIn();

        Expense::factory(10)->create([
            'amount' => 1000,
            'user_id' => $user->id,
            'team_id' => $user->currentTeam->id,
        ]);

        $this
            ->get(route('expenses.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Expenses/Index')
                ->has('expenses', 10)
                ->has('monthlyTotals', 5)
                ->where('monthlyTotals.2.total', 100)
            );
    }

    /** @test */
    public function a_team_cannot_have_more_than_x_expenses_per_month()
    {
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
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('errors')
                ->where('errors.date', 'Too many expenses logged on this team this month.')
            );
    }

    /** @test */
    public function expense_date_must_be_within_a_valid_date_range()
    {
        $this->travelTo('2024-01-01');

        $user = $this->signIn();

        // test error messages on create
        $attributes = Expense::factory()->recycle($user)->raw([
            'date' => now()->addYears(2),
        ]);

        $this->followingRedirects()
            ->put(route('expenses.create'), $attributes)
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('errors')
                ->where('errors.date', 'The date cannot be more than 2 years in the future.')
            );

        $attributes = Expense::factory()->recycle($user)->raw([
            'date' => now()->subYear(),
        ]);

        $this->followingRedirects()
            ->put(route('expenses.create'), $attributes)
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('errors')
                ->where('errors.date', 'The date cannot be more than 1 year in the past.')
            );

        // test error messages on update
        $expense = Expense::factory()->recycle($user)->create([
            'amount' => 1000,
        ]);

        $expense->amount = 2000;
        $expense->date = now()->addYears(2);

        $this->followingRedirects()
            ->patch(route('expenses.update', $expense), $expense->toArray())
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('errors')
                ->where('errors.date', 'The date cannot be more than 2 years in the future.')
            );

        $expense->amount = 2000;
        $expense->date = now()->subYear();

        $this->followingRedirects()
            ->patch(route('expenses.update', $expense), $expense->toArray())
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('errors')
                ->where('errors.date', 'The date cannot be more than 1 year in the past.')
            );
    }
}
