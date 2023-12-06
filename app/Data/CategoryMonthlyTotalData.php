<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class CategoryMonthlyTotalData extends Data
{
    public function __construct(
        public int $id,
        public int $amount,
        public int $year_month,
        public CategoryData $category,
    ) {

    }
}
