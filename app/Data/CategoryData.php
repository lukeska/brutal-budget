<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class CategoryData extends Data
{
    public function __construct(
        public int|Optional $id,
        public string $name,
        public string $icon,
        public string $hex,
    ) {
    }
}
