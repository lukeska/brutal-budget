<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class ExpensesIndexPage extends Data
{
    public function __construct(
        /** @var DataCollection<ExpenseData> */
        public DataCollection $expenses,
        /** @var DataCollection<MonthlyTotalData> */
        public DataCollection $monthlyTotals,
        public string $expensesView,
    ) {
    }
}
