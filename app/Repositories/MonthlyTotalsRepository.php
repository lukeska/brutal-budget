<?php

namespace App\Repositories;

use App\Data\CategoryMonthlyTotalData;
use App\Data\MonthlyTotalData;
use App\Models\CategoryMonthlyTotal;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelData\DataCollection;

class MonthlyTotalsRepository
{
    public function getAll(int $teamId, ?int $year = null, ?int $month = null, ?bool $regularExpenses = null, int $previousMonthsOffset = 2, int $followingMonthsOffset = 2): DataCollection
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
            $rawTotals = $this->get($teamId, $date, $regularExpenses);

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

    public function get(int $teamId, Carbon $date, ?bool $regular = null): Collection
    {
        $key = "monthlyTotal-get-{$teamId}-{$date->format('Ym')}-{$regular}";
        $tags = $this->getCacheTags($teamId, $date);

        if (Cache::tags($tags)->has($key)) {
            $total = Cache::tags($tags)->get($key);
        } else {
            $total = CategoryMonthlyTotal::query()
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
                ->where('team_id', $teamId)
                ->where('year_month', $date->format('Ym'))
                ->whereNull('user_id')
                ->where('is_regular', $regular)
                ->with('category')
                ->orderByDesc('amount')
                ->orderByDesc('id')
                ->get();

            Cache::tags($tags)->put($key, $total);
        }

        return $total;
    }

    public function getCacheTags(int $teamId, \Carbon\Carbon $date): array
    {
        return ["monthlytotals-{$teamId}-{$date->format('Ym')}"];
    }

    public function flushCache(int $teamId, \Carbon\Carbon $date): void
    {
        Cache::tags($this->getCacheTags($teamId, $date))->flush();
    }
}
