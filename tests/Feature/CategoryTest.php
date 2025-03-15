<?php

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

pest()->group('brutal');

test('a user can create a category', function () {
    $this->signIn();

    $attributes = Category::factory([
        'name' => 'AAA My test category',
    ])->raw();

    $this->get(route('categories.index'))->assertOk();

    $this->followingRedirects()
        ->put(route('categories.create'), $attributes)
        ->assertOk()
        ->assertInertia(fn(AssertableInertia $page) => $page
            ->component('Categories/Index')
            ->where('categories.0.name', 'AAA My test category')
        );
});

test('a user cannot edit categories for a group he is not part of', function () {
    $user = $this->signIn();

    $user2 = User::factory()->withPersonalTeam()->create();
    $category = $user2->currentTeam->categories()->first();

    $this->followingRedirects()
        ->patch(route('categories.update', ['category' => $category->id]), [
            'name' => 'My test category',
            'icon' => $category->icon,
            'hex' => $category->hex,
        ])
        ->assertStatus(403);
});

test('a category name must be unique within a team', function () {
    $user = $this->signIn();

    $category1 = Category::factory([
        'name' => 'My test category',
        'team_id' => $user->currentTeam->id,
    ])->create();

    $category2 = Category::factory([
        'name' => 'My test category 2',
        'team_id' => $user->currentTeam->id,
    ])->create();

    $team = $user->ownedTeams()->create([
        'name' => 'Second Team',
        'personal_team' => false,
    ]);

    $attributes = Category::factory([
        'name' => 'My test category',
    ])->raw();

    // Creating a duplicate category name within the same team should fail.
    $this->get(route('categories.index'));
    $this->followingRedirects()
        ->put(route('categories.create'), $attributes)
        ->assertOk()
        ->assertInertia(fn(AssertableInertia $page) => $page
            ->has('errors')
            ->where('errors.name', 'The name has already been taken.')
        );

    // Updating an existing category with a duplicate name should fail.
    $this->followingRedirects()
        ->patch(route('categories.update', ['category' => $category1->id]), [
            'name' => 'My test category 2',
        ])
        ->assertOk()
        ->assertInertia(fn(AssertableInertia $page) => $page
            ->has('errors')
            ->where('errors.name', 'The name has already been taken.')
        );

    $user->switchTeam($team);

    $category3 = Category::factory([
        'name' => 'My test category 3',
        'team_id' => $user->currentTeam->id,
    ])->create();

    // Creating a duplicate category name in a different team should succeed.
    $this->followingRedirects()
        ->put(route('categories.create'), $attributes)
        ->assertOk()
        ->assertInertia(fn(AssertableInertia $page) => $page->has('errors', 0));

    // Updating a duplicate category name in a different team should succeed.
    $this->followingRedirects()
        ->patch(route('categories.update', ['category' => $category3->id]), [
            'name' => 'My test category 2',
            'icon' => $category3->icon,
            'hex' => $category3->hex,
        ])
        ->assertOk()
        ->assertInertia(fn(AssertableInertia $page) => $page->has('errors', 0));
});

test('a category cannot be deleted if there are expenses associated to it', function () {
    $user = $this->signIn();
    $category = $user->currentTeam->categories->first();

    Expense::factory()->create([
        'amount' => 1000,
        'user_id' => $user->id,
        'team_id' => $user->currentTeam->id,
        'category_id' => $category->id,
    ]);

    $this->followingRedirects()
        ->delete(route('categories.delete', ['category' => $category->id]))
        ->assertStatus(403);
});

test('a team cannot have more than x categories', function () {
    Config::set('global.limits.categories_per_team', 1);

    $user = $this->signIn();

    Category::factory()
        ->recycle($user->currentTeam)
        ->count(config('global.limits.categories_per_team'))
        ->create();

    $attributes = Category::factory([
        'name' => 'My test category',
    ])->raw();

    $this->get(route('categories.index'));

    $this->followingRedirects()
        ->put(route('categories.create'), $attributes)
        ->assertOk()
        ->assertInertia(fn(AssertableInertia $page) => $page
            ->has('errors')
            ->where('errors.name', 'You reach the limit of categories this team can have.')
        );
});
