<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\OnboardingSteps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use NotificationChannels\WebPush\HasPushSubscriptions;

/**
 * @property Team $currentTeam
 */
class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use HasPushSubscriptions;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'currency_id', 'secondary_currency_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function secondaryCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'secondary_currency_id');
    }

    public function onboardingStatuses(): HasMany
    {
        return $this->hasMany(OnboardingStatus::class)
            ->orderBy('order');
    }

    public function onboardingStatusExpenseCreated(): HasOne
    {
        return $this->hasOne(OnboardingStatus::class)
            ->where('onboarding_step', OnboardingSteps::ExpenseCreated);
    }

    public function onboardingStatusProjectCreated(): HasOne
    {
        return $this->hasOne(OnboardingStatus::class)
            ->where('onboarding_step', OnboardingSteps::ProjectCreated);
    }

    public function onboardingStatusTeamMemberInvited(): HasOne
    {
        return $this->hasOne(OnboardingStatus::class)
            ->where('onboarding_step', OnboardingSteps::TeamMemberInvited);
    }
}
