<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class MonthlyTotalData extends Data
{
    public function __construct(
        public int $total,
        public int $yearMonth,
        /** @var DataCollection<CategoryMonthlyTotalData> */
        public DataCollection $categoryMonthlyTotals,
        public bool $isCurrent,
    ) {

    }
}
