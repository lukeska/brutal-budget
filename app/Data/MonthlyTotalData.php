<?php

namespace App\Data;

use App\Data\Transformers\IntToCurrencyTransformer;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class MonthlyTotalData extends Data
{
    public function __construct(
        #[WithTransformer(IntToCurrencyTransformer::class)]
        public int $total,
        public int $yearMonth,
        /** @var DataCollection<CategoryMonthlyTotalData> */
        public DataCollection $categoryMonthlyTotals,
        public bool $isCurrent,
    ) {

    }
}
