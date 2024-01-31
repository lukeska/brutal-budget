<?php

namespace App\Data\Transformers;

use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Transformers\Transformer;

class IntToCurrencyTransformer implements Transformer
{
    public function transform(DataProperty $property, mixed $value): mixed
    {
        return round(floatval($value) / 100, precision: 2);
    }
}
