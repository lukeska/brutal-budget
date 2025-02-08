<?php

namespace App\Models;

use App\Observers\ExpenseObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Laravel\Jetstream\Jetstream;

/**
 * @property Team $team
 * @property string $formatted_amount
 */
#[ObservedBy([ExpenseObserver::class])]
class Expense extends Model
{
    /** @use HasFactory<\Database\Factories\ExpenseFactory> */
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Jetstream::teamModel());
    }

    public function convertAmountTo(int $currencyId): float
    {
        $exchangeRate = CurrencyExchangeRate::query()
            ->where('from_currency_id', $this->currency_id)
            ->where('to_currency_id', $currencyId)
            ->first()->rate;

        return $this->amount * $exchangeRate;
    }

    /*
     * Accessors
     */
    protected function formattedAmount(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
                $formatter->setTextAttribute(NumberFormatter::CURRENCY_CODE, $this->user->currency);

                return $formatter->formatCurrency($attributes['amount'] / 100, $this->user->currency);
            },
        );
    }

    /*
     * Scopes
     */
    public function scopeMonth(Builder $query, ?Carbon $date = null): void
    {
        $date = $date ?: Carbon::now();

        $query->whereBetween('date', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()]);
    }

    public function scopeMonthFromInt(Builder $query, ?int $year_month = null): void
    {
        $date = $year_month ? Carbon::create(
            Str::of((string)$year_month)->substr(0, 4)->toInteger(),
            Str::of((string)$year_month)->substr(4, 2)->toInteger(),
        ) : Carbon::now();

        $query->whereBetween('date', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()]);
    }
}
