<?php

namespace App\Enums;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
enum OnboardingSteps: string
{
    case ExpenseCreated = 'expense-created';
    case TeamMemberInvited = 'team-member-invited';
    case ProjectCreated = 'project-created';
}
