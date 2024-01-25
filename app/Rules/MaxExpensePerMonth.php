<?php

namespace App\Rules;

use App\Models\Expense;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class MaxExpensePerMonth implements DataAwareRule, ValidationRule
{
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    public function __construct(protected int $teamId)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $date = CarbonImmutable::parse($this->data['date']);

        $expenseCount = Expense::query()
            ->where('team_id', $this->teamId)
            ->whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->count();

        if ($expenseCount > config('global.limits.expenses_per_month')) {
            $fail('Too many expenses logged on this team this month.');
        }
    }

    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }
}
