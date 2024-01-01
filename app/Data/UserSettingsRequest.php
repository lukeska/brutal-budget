<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class UserSettingsRequest extends Data
{
    public function __construct(
        public string $currency
    ) {
    }
}
