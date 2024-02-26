<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** @group brutal */
class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_generates_onboarding_statuses_when_a_user_is_created()
    {
        $user = User::factory()->create();

        $this->assertCount(3, $user->onboardingStatuses);
    }
}
