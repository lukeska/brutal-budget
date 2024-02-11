<?php

use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/** @group brutal */
class UserRolesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_view_other_users_expenses_as_an_editor()
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $expense = Expense::factory()->recycle($owner)->create();

        $editor = $this->signIn();

        $owner->currentTeam->users()->attach($editor, ['role' => 'editor']);
        $editor->switchTeam($owner->currentTeam);

        $this->get(route('expenses.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('expenses', 1)
                ->where('expenses.0.amount', $expense->amount / 100)
            );
    }

    /** @test */
    public function it_cannot_edit_other_users_expenses_as_an_editor()
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $expense = Expense::factory()->recycle($owner)->create();

        $editor = $this->signIn();

        $owner->currentTeam->users()->attach($editor, ['role' => 'editor']);
        $editor->switchTeam($owner->currentTeam);

        $expense->amount = 1100;

        $this->followingRedirects()
            ->patch(route('expenses.update', $expense), $expense->toArray())
            ->assertStatus(403);
    }
}
