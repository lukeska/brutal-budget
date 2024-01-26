<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/** @group brutal */
class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_create_a_category(): void
    {
        $this->signIn();

        $attributes = Category::factory([
            'name' => 'AAA My test category',
        ])->raw();

        $this->followingRedirects()
            ->put(route('categories.create'), $attributes)
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Categories/Index')
                ->where('categories.0.name', 'AAA My test category')
            );
    }

    /** @test */
    public function a_user_cannot_edit_categories_for_a_group_he_is_not_part_of(): void
    {
        $user = $this->signIn();

        $user2 = User::factory()->withPersonalTeam()->create();

        $category = $user2->currentTeam->categories()->first();

        $this->followingRedirects()
            ->patch(route('categories.update', ['category' => $category->id]), ['name' => 'My test category', 'icon' => $category->icon, 'hex' => $category->hex])
            ->assertStatus(403);
    }

    /** @test */
    public function a_category_name_must_be_unique_within_a_team(): void
    {
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

        // create a duplicate category name within the same team should fail
        $this->followingRedirects()
            ->put(route('categories.create'), $attributes)
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('errors')
                ->where('errors.name', 'The name has already been taken.')
            );

        // update an existing category with a duplicate name should fail
        $this->followingRedirects()
            ->patch(route('categories.update', ['category' => $category1->id]), ['name' => 'My test category 2'])
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('errors')->where('errors.name', 'The name has already been taken.')
            );

        $user->switchTeam($team);

        $category3 = Category::factory([
            'name' => 'My test category 3',
            'team_id' => $user->currentTeam->id,
        ])->create();

        // create a duplicate category name in a different team should succeed
        $this->followingRedirects()
            ->put(route('categories.create'), $attributes)
            ->assertOk()
            ->assertInertia(function (AssertableInertia $page) {
                return $page->has('errors', 0);
            }
            );

        // update a duplicate category name in a different team should succeed
        $this->followingRedirects()
            ->patch(route('categories.update', ['category' => $category3->id]), ['name' => 'My test category 2', 'icon' => $category3->icon, 'hex' => $category3->hex])
            ->assertOk()
            ->assertInertia(function (AssertableInertia $page) {
                return $page->has('errors', 0);
            }
            );
    }

    /** @test */
    public function a_category_cannot_be_deleted_if_there_are_expenses_associated_to_it(): void
    {
        /** @var User $user */
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
    }

    /** @test */
    public function a_team_cannot_have_more_than_x_categories()
    {
        Config::set('global.limits.categories_per_team', 1);

        $user = $this->signIn();

        Category::factory()
            ->recycle($user->currentTeam)
            ->count(config('global.limits.categories_per_team'))
            ->create();

        $attributes = Category::factory([
            'name' => 'My test category',
        ])->raw();

        $this->followingRedirects()
            ->put(route('categories.create'), $attributes)
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('errors')
                ->where('errors.limit', 'You reach the limit of categories this team can have.')
            );
    }
}
