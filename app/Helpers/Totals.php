<?php

namespace App\Helpers;

use App\Models\CategoryMonthlyTotal;
use App\Models\Expense;
use Illuminate\Support\Str;

class Totals
{
    public function generateByCategory(int $category_id, int $team_id, int $year_month): void
    {
        $expenses = Expense::where('category_id', $category_id)
            ->where('team_id', $team_id)
            ->whereMonth('date', Str::of($year_month)->substr(4,2)->toInteger())
            ->whereYear('date', Str::of($year_month)->substr(0,4)->toInteger())
            ->get();

        $expensesTotalAmount = $expenses->sum('amount');

        $teamTotal = CategoryMonthlyTotal::where('year_month', $year_month)
            ->where('category_id', $category_id)
            ->where('team_id', $team_id)
            ->whereNull('user_id')
            ->first();

        if($expensesTotalAmount == 0 && $teamTotal) {

            $teamTotal->delete();

        } else if($teamTotal) {

            $teamTotal->update([
                'amount' => $expensesTotalAmount,
            ]);

        } else {

            CategoryMonthlyTotal::create([
                'category_id' => $category_id,
                'team_id' => $team_id,
                'year_month' => $year_month,
                'amount' => $expensesTotalAmount,
            ]);

        }
    }
}
