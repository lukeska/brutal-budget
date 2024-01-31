<?php

namespace App\Data\Casts;

use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\DataProperty;

class CurrencyToIntCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $context): mixed
    {
        return round(floatval($value) * 100);
    }
}
