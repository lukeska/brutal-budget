<?php

namespace App\Enums;

enum OnboardingSteps: int
{
    case ExpenseCreated = 1;
    case TeamMemberInvited = 2;
    case ProjectCreated = 3;
}
