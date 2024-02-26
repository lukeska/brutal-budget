<?php

namespace App\Observers;

use App\Enums\OnboardingSteps;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $user->onboardingStatuses()->create([
            'onboarding_step_id' => OnboardingSteps::ExpenseCreated,
        ]);

        $user->onboardingStatuses()->create([
            'onboarding_step_id' => OnboardingSteps::TeamMemberInvited,
        ]);

        $user->onboardingStatuses()->create([
            'onboarding_step_id' => OnboardingSteps::ProjectCreated,
        ]);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
