<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryMonthlyTotal extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryMonthlyTotalFactory> */
    use HasFactory;

    protected $guarded = [];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function getFormattedAmountAttribute($value): string
    {
        return number_format($value / 100, 2, '.', '');
    }
}
