<?php

namespace App\Data;

use App\Data\Transformers\IntToCurrencyTransformer;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;

class CategoryTotalData extends Data
{
    public function __construct(
        #[MapName('category_id')]
        public int $categoryId,
        #[WithTransformer(IntToCurrencyTransformer::class)]
        public int $total,
    ) {
    }
}
