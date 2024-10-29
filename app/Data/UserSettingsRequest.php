<?php

namespace App\Data;

use App\Models\Currency;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;

class UserSettingsRequest extends Data
{
    public function __construct(
        #[MapName('currency_id')]
        public int $currencyId
    ) {
    }
}
