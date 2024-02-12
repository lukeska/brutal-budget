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

    protected $owner;

    protected $admin;

    protected $editor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->withPersonalTeam()->create();
        $this->admin = User::factory()->withPersonalTeam()->create();
        $this->editor = User::factory()->withPersonalTeam()->create();

        $this->owner->currentTeam->users()->attach($this->admin, ['role' => 'admin']);
        $this->admin->switchTeam($this->owner->currentTeam);

        $this->owner->currentTeam->users()->attach($this->editor, ['role' => 'editor']);
        $this->editor->switchTeam($this->owner->currentTeam);

        $this->expense = Expense::factory()->recycle($this->owner)->create();
    }

    /** @test */
    public function it_can_view_other_users_expenses_as_an_editor()
    {
        $this->actingAs($this->editor);

        $this->get(route('expenses.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('expenses', 1)
                ->where('expenses.0.amount', $this->expense->amount / 100)
            );
    }

    /** @test */
    public function it_cannot_edit_other_users_expenses_as_an_editor()
    {
        $this->actingAs($this->editor);

        $this->expense->amount = 1100;

        $this->followingRedirects()
            ->patch(route('expenses.update', $this->expense), $this->expense->toArray())
            ->assertStatus(403);
    }

    /** @test */
    public function it_cannot_delete_other_users_expenses_as_an_editor()
    {
        $this->actingAs($this->editor);

        $this->followingRedirects()
            ->delete(route('expenses.delete', $this->expense), $this->expense->toArray())
            ->assertStatus(403);
    }
}
