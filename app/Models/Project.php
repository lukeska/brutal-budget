<?php

namespace App\Models;

use App\Observers\ProjectObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Jetstream\Jetstream;

#[ObservedBy([ProjectObserver::class])]
class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'hex',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Jetstream::teamModel());
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
