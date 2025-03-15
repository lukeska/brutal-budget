<?php

use App\Models\Category;
use App\Models\Expense;
use App\Models\Project;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

pest()->group('brutal');

beforeEach(function () {
    $this->owner = User::factory()->withPersonalTeam()->create();
    $this->admin = User::factory()->withPersonalTeam()->create();
    $this->editor = User::factory()->withPersonalTeam()->create();

    $this->owner->currentTeam->users()->attach($this->admin, ['role' => 'admin']);
    $this->admin->switchTeam($this->owner->currentTeam);

    $this->owner->currentTeam->users()->attach($this->editor, ['role' => 'editor']);
    $this->editor->switchTeam($this->owner->currentTeam);

    $this->expense = Expense::factory()->recycle($this->owner)->create();
    $this->project = Project::factory()
        ->recycle($this->owner)
        ->recycle($this->owner->currentTeam)
        ->create();
});

test('it can view other users expenses as an editor', function () {
    $this->actingAs($this->editor);

    $this->get(route('expenses.index'))
        ->assertOk()
        ->assertInertia(fn(AssertableInertia $page) => $page
            ->has('expenses', 1)
            ->where('expenses.0.amount', $this->expense->amount / 100)
        );
});

test('it cannot edit other users expenses as an editor', function () {
    $this->actingAs($this->editor);

    $this->expense->amount = 1100;

    $this->followingRedirects()
        ->patch(route('expenses.update', $this->expense), $this->expense->toArray())
        ->assertStatus(403);
});

test('it cannot delete other users expenses as an editor', function () {
    $this->actingAs($this->editor);

    $this->followingRedirects()
        ->delete(route('expenses.delete', $this->expense), $this->expense->toArray())
        ->assertStatus(403);
});

test('it cannot create categories as an editor', function () {
    $this->actingAs($this->editor);

    $attributes = Category::factory()->raw();

    $this->followingRedirects()
        ->put(route('categories.create'), $attributes)
        ->assertStatus(403);
});

test('it cannot edit categories as an editor', function () {
    $this->actingAs($this->editor);

    $category = $this->editor->currentTeam->categories()->first();

    $this->followingRedirects()
        ->patch(route('categories.update', ['category' => $category->id]), [
            'name' => 'My test category',
            'icon' => $category->icon,
            'hex' => $category->hex,
        ])
        ->assertStatus(403);
});

test('it cannot delete categories as an editor', function () {
    $this->actingAs($this->editor);

    $category = $this->editor->currentTeam->categories()->first();

    $this->followingRedirects()
        ->delete(route('categories.delete', ['category' => $category->id]))
        ->assertStatus(403);
});

test('it cannot create projects as an editor', function () {
    $this->actingAs($this->editor);

    $attributes = Project::factory()->raw();

    $this->followingRedirects()
        ->put(route('projects.create'), $attributes)
        ->assertStatus(403);
});

test('it cannot edit projects as an editor', function () {
    $this->actingAs($this->editor);

    $this->followingRedirects()
        ->patch(route('projects.update', ['project' => $this->project->id]), [
            'name' => 'My test project',
            'hex' => $this->project->hex,
        ])
        ->assertStatus(403);
});

test('it cannot delete projects as an editor', function () {
    $this->actingAs($this->editor);

    $this->followingRedirects()
        ->delete(route('projects.delete', ['project' => $this->project->id]))
        ->assertStatus(403);
});
