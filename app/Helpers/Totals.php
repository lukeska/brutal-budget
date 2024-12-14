<?php

namespace App\Helpers;

use App\Models\CategoryMonthlyTotal;
use App\Models\Expense;

class Totals
{
    public function generateByCategory(int $categoryId, int $teamId, int $currencyId, int $yearMonth): void
    {
        $this->generate($categoryId, $teamId, $currencyId, $yearMonth, isRegular: null);
        $this->generate($categoryId, $teamId, $currencyId, $yearMonth, isRegular: true);
        $this->generate($categoryId, $teamId, $currencyId, $yearMonth, isRegular: false);
    }

    protected function generate(int $categoryId, int $teamId, int $currencyId, int $yearMonth, ?bool $isRegular = null): void
    {
        /*
         * TODO: must generate total given expenses that can be in different currencies.
         * The currency to choose is the one selected by the user which must be passed in
         */
        $expensesTotalAmount = Expense::query()
            ->selectRaw('SUM(
                CASE
                    WHEN currency_id = '.$currencyId.' THEN amount
                    ELSE ROUND(amount * (
                        SELECT rate
                        FROM currency_exchange_rates
                        WHERE from_currency_id = expenses.currency_id
                        AND to_currency_id = '.$currencyId.'
                        LIMIT 1
                    ))
                END
            ) as total_amount')
            ->monthFromInt($yearMonth)
            ->where('category_id', $categoryId)
            ->where('team_id', $teamId)
            ->when($isRegular !== null, function ($query) use ($isRegular) {
                return $query->where('is_regular', $isRegular);
            })
            ->first()->total_amount;

        $teamTotal = CategoryMonthlyTotal::where('year_month', $yearMonth)
            ->where('category_id', $categoryId)
            ->where('team_id', $teamId)
            ->whereNull('user_id')
            ->when($isRegular !== null, function ($query) use ($isRegular) {
                return $query->where('is_regular', $isRegular);
            })
            ->first();

        if ($expensesTotalAmount == 0 && $teamTotal) {

            $teamTotal->delete();

        } elseif ($teamTotal) {

            $teamTotal->update([
                'amount' => $expensesTotalAmount,
                'currency_id' => $currencyId,
            ]);

        } elseif ($expensesTotalAmount > 0) {

            CategoryMonthlyTotal::create([
                'category_id' => $categoryId,
                'team_id' => $teamId,
                'year_month' => $yearMonth,
                'amount' => $expensesTotalAmount,
                'currency_id' => $currencyId,
                'is_regular' => $isRegular,
            ]);

        }
    }
}
