<?php

namespace Tests\Feature;

use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        Expense::factory()->recycle($user)->create();

        $this->assertTrue($user->onboardingStatusExpenseCreated->fresh()->done());
    }
}
