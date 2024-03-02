<?php

namespace App\Models;

use App\Enums\OnboardingSteps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'order',
        'skipped_at',
        'completed_at',
        'onboarding_step',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'onboarding_step' => OnboardingSteps::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
