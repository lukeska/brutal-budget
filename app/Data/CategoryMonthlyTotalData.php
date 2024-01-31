<?php

namespace App\Data;

use App\Data\Transformers\IntToCurrencyTransformer;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;

class CategoryMonthlyTotalData extends Data
{
    public function __construct(
        public int $id,
        #[WithTransformer(IntToCurrencyTransformer::class)]
        public int $amount,
        public int $year_month,
        public CategoryData $category,
        #[WithTransformer(IntToCurrencyTransformer::class)]
        public ?int $previous_month_delta_amount,
    ) {

    }
}
