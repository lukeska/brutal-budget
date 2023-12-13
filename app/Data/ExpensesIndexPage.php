<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class ExpensesIndexPage extends Data
{
    public function __construct(
        /** @var DataCollection<ExpenseData> */
        public DataCollection $expenses,
        /** @var DataCollection<CategoryMonthlyTotalData> */
        public DataCollection $categoryMonthlyTotals,
        /** @var DataCollection<CategoryMonthlyTotalData> */
        public DataCollection $categoryMonthlyTotalsPreviousMonth,
        /** @var DataCollection<CategoryMonthlyTotalData> */
        public DataCollection $categoryMonthlyTotalsFollowingMonth,
        public int $totalExpenses,
        public int $totalExpensesPreviousMonth,
        public int $totalExpensesFollowingMonth,
        public int $year,
        public int $month
    )
    {
    }
}
