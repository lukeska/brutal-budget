<?php

namespace App\Data;

use App\Enums\OnboardingSteps;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;

class OnboardingStatusData extends Data
{
    public function __construct(
        public int $id,
        #[MapName('user_id')]
        public int $userId,
        #[MapName('onboarding_step')]
        public OnboardingSteps $onboardingStep,
        #[MapName('skipped_at')]
        public ?Carbon $skippedAt,
        #[MapName('completed_at')]
        public ?Carbon $completedAt,
    ) {
    }
}
