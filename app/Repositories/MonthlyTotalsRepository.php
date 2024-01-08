<?php

namespace App\Repositories;

use App\Data\CategoryMonthlyTotalData;
use App\Data\MonthlyTotalData;
use App\Models\CategoryMonthlyTotal;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelData\DataCollection;

class MonthlyTotalsRepository
{
    public function getMonthlyTotals($year = null, $month = null, $regularExpenses = null, $previousMonthsOffset = 2, $followingMonthsOffset = 2): DataCollection
    {
        $currentDate = Carbon::now()->firstOfMonth();

        if ($year && $month) {
            $currentDate = Carbon::create($year, $month, 1);
        }

        $dates = [];
        if ($previousMonthsOffset > 0) {
            foreach (range(($previousMonthsOffset + 1) * -1, -1) as $monthOffset) {
                $dates[] = $currentDate->clone()->addMonth($monthOffset);
            }
        }

        $dates[] = $currentDate;

        if ($followingMonthsOffset > 0) {
            foreach (range(1, $followingMonthsOffset) as $monthOffset) {
                $dates[] = $currentDate->clone()->addMonth($monthOffset);
            }
        }

        $data = [];
        foreach ($dates as $date) {
            $rawTotals = CategoryMonthlyTotal::query()
                ->select(
                    '*',
                    DB::raw('(amount - (
                        SELECT amount
                        FROM category_monthly_totals AS t2
                        WHERE t2.category_id = category_monthly_totals.category_id
                          AND t2.year_month = '.$date->clone()->subMonth()->format('Ym').
                        ' ORDER BY t2.year_month DESC
                        LIMIT 1
                    )) as previous_month_delta_amount')
                )
                ->where('team_id', Auth::user()->currentTeam->id)
                ->where('year_month', $date->format('Ym'))
                ->whereNull('user_id')
                ->where('is_regular', $regularExpenses)
                ->with('category')
                ->orderByDesc('amount')
                ->get();

            $totalExpenses = $rawTotals->sum('amount');

            $data[] = MonthlyTotalData::from([
                'total' => $totalExpenses,
                'yearMonth' => $date->format('Ym'),
                'categoryMonthlyTotals' => CategoryMonthlyTotalData::collection($rawTotals),
                'isCurrent' => $currentDate->eq($date),
            ]);
        }

        return MonthlyTotalData::collection(array_slice($data, 1));
    }
}
