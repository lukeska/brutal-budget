<?php

namespace App\Helpers;

use App\Models\CategoryMonthlyTotal;
use App\Models\Expense;

class Totals
{
    public function generateByCategory(int $categoryId, int $teamId, int $yearMonth): void
    {
        $this->generate($categoryId, $teamId, $yearMonth, isRegular: null);
        $this->generate($categoryId, $teamId, $yearMonth, isRegular: true);
        $this->generate($categoryId, $teamId, $yearMonth, isRegular: false);
    }

    protected function generate(int $categoryId, int $teamId, int $yearMonth, ?bool $isRegular = null): void
    {
        $expenses = Expense::query()
            ->monthFromInt($yearMonth)
            ->where('category_id', $categoryId)
            ->where('team_id', $teamId)
            ->when($isRegular !== null, function ($query) use ($isRegular) {
                return $query->where('is_regular', $isRegular);
            })
            ->get();

        $expensesTotalAmount = $expenses->sum('amount');

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
            ]);

        } elseif ($expensesTotalAmount > 0) {

            CategoryMonthlyTotal::create([
                'category_id' => $categoryId,
                'team_id' => $teamId,
                'year_month' => $yearMonth,
                'amount' => $expensesTotalAmount,
                'is_regular' => $isRegular,
            ]);

        }
    }
}
