<?php

namespace App\Data;

use Illuminate\Database\Eloquent\Collection;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class ExpensesIndexPage extends Data
{
    public function __construct(
        /** @var Collection<ExpenseData> */
        public Collection $expenses,
        /** @var array<MonthlyTotalData> */
        public array      $monthlyTotals,
    )
    {
    }
}
