<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/** @group brutal */
class OnboardingStatusTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_updates_the_status_when_first_expense_is_created()
    {
        $user = $this->signIn();

        $this->assertFalse($user->onboardingStatusExpenseCreated->done());

        $attributes = Expense::factory()->recycle($user)->raw();

        $this->put(route('expenses.create'), $attributes);

        $this->assertTrue($user->onboardingStatusExpenseCreated->fresh()->done());
    }

    /** @test */
    public function it_updates_the_status_when_first_project_is_created()
    {
        $user = $this->signIn();

        $this->assertFalse($user->onboardingStatusProjectCreated->done());

        $attributes = Project::factory()->recycle($user->currentTeam)->raw();

        $this->put(route('projects.create'), $attributes);

        $this->assertTrue($user->onboardingStatusProjectCreated->fresh()->done());
    }
}
