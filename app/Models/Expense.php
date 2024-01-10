<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Laravel\Jetstream\Jetstream;

class Expense extends Model
{
    use HasFactory;

    protected $guarded = [];

    /*protected $casts = [
        'date' => 'datetime',
    ];*/

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

    public function team(): BelongsTo
    {
        return $this->belongsTo(Jetstream::teamModel());
    }

    /*public function getAmountAttribute($value): string
    {
        return number_format($value / 100, 2, '.', '');
    }

    public function setAmountAttribute($value): void
    {
        $this->attributes['amount'] = $value * 100;
    }*/

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
            Str::of($year_month)->substr(0, 4)->toInteger(),
            Str::of($year_month)->substr(4, 2)->toInteger(),
        ) : Carbon::now();

        $query->whereBetween('date', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()]);
    }
}
