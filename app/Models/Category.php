<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Jetstream\Jetstream;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'hex',
        'icon',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Jetstream::teamModel());
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function monthlyTotals(): HasMany
    {
        return $this->hasMany(CategoryMonthlyTotal::class);
    }
}
