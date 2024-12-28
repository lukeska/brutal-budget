<?php

namespace App\Repositories;

use App\Data\CategoryMonthlyTotalData;
use App\Data\CategoryTotalData;
use App\Data\MonthlyTotalData;
use App\Models\CategoryMonthlyTotal;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelData\DataCollection;

class MonthlyTotalsRepository
{
    public function getAll(int $teamId, int $currencyId, ?int $categoryId = null, ?int $year = null, ?int $month = null, ?bool $regularExpenses = null, int $previousMonthsOffset = 2, int $followingMonthsOffset = 2): DataCollection
    {
        $currentDate = Carbon::now()->firstOfMonth();

        if ($year && $month) {
            $currentDate = Carbon::create($year, $month, 1);
        }

        $dates = [];
        if ($previousMonthsOffset > -1) {
            foreach (range(($previousMonthsOffset + 1) * -1, -1) as $monthOffset) {
                $dates[] = $currentDate->clone()->addMonths($monthOffset);
            }
        }

        $dates[] = $currentDate;

        if ($followingMonthsOffset > 0) {
            foreach (range(1, $followingMonthsOffset) as $monthOffset) {
                $dates[] = $currentDate->clone()->addMonths($monthOffset);
            }
        }

        $data = [];
        foreach ($dates as $date) {
            $rawTotals = $this->get(
                teamId: $teamId,
                date: $date,
                currencyId: $currencyId,
                categoryId: $categoryId,
                regular: $regularExpenses
            );

            $totalExpenses = $rawTotals->sum('converted_amount');

            $data[] = MonthlyTotalData::from([
                'total' => $totalExpenses,
                'yearMonth' => $date->format('Ym'),
                'categoryMonthlyTotals' => CategoryMonthlyTotalData::collection($rawTotals),
                'isCurrent' => $currentDate->eq($date),
            ]);
        }

        return MonthlyTotalData::collection(array_slice($data, 1));
    }

    public function get(int $teamId, Carbon $date, int $currencyId, ?int $categoryId = null, ?bool $regular = null): Collection
    {
        $key = "monthlyTotal-get-{$teamId}-{$currencyId}-{$categoryId}-{$date->format('Ym')}-{$regular}";
        $tags = $this->getCacheTags($teamId, $date);

        if (Cache::tags($tags)->has($key)) {
            $total = Cache::tags($tags)->get($key);
        } else {
            $total = CategoryMonthlyTotal::query()
                ->select(
                    'category_monthly_totals.*',
                    DB::raw('((amount * rate) - ((
                        SELECT amount  * cer.rate
                        FROM category_monthly_totals AS t2
                        LEFT JOIN currency_exchange_rates AS cer
                        ON t2.currency_id = cer.from_currency_id
                        WHERE cer.to_currency_id = ' . $currencyId . '
                          AND t2.category_id = category_monthly_totals.category_id
                          AND t2.year_month = ' . $date->clone()->subMonth()->format('Ym') .
                        ' ORDER BY t2.year_month DESC
                        LIMIT 1
                    ))) as previous_month_delta_amount'),
                    DB::raw('amount * rate AS converted_amount')
                )
                ->leftJoin('currency_exchange_rates', function ($join) use ($currencyId) {
                    $join->on('category_monthly_totals.currency_id', '=', 'currency_exchange_rates.from_currency_id')
                        ->where('currency_exchange_rates.to_currency_id', '=', $currencyId);
                })
                ->where('team_id', $teamId)
                ->where('year_month', $date->format('Ym'))
                ->whereNull('user_id')
                ->where('is_regular', $regular)
                ->when($categoryId != null, function ($query) use ($categoryId) {
                    $query->where('category_monthly_totals.category_id', '=', $categoryId);
                })
                ->with('category')
                ->with('currency')
                ->orderByDesc('converted_amount')
                ->orderByDesc('id')
                ->get();

            Cache::tags($tags)->put($key, $total);
        }

        return $total;
    }

    public function getCategoriesTotals(int $teamId, int $currencyId): DataCollection
    {
        $key = "monthlyTotal-getCategoriesTotals-{$teamId}-{$currencyId}";
        $tags = $this->getCacheTags($teamId);

        if (Cache::tags($tags)->has($key)) {
            $categoryTotals = Cache::tags($tags)->get($key);
        } else {
            $categoryTotalsRaw = CategoryMonthlyTotal::query()
                ->join('currency_exchange_rates', 'category_monthly_totals.currency_id', '=', 'currency_exchange_rates.from_currency_id')
                ->where('currency_exchange_rates.to_currency_id', '=', $currencyId) // Ensure the rates are for the correct base currency
                ->groupBy('category_monthly_totals.category_id')
                ->selectRaw('category_monthly_totals.category_id, SUM(category_monthly_totals.amount * currency_exchange_rates.rate) as total')
                ->where('category_monthly_totals.team_id', $teamId)
                ->whereNull('category_monthly_totals.is_regular')
                ->get();

            $categoryTotals = CategoryTotalData::collection($categoryTotalsRaw->toArray());
        }

        return $categoryTotals;
    }

    public function getCacheTags(int $teamId, ?Carbon $date = null): array
    {
        if ($date === null) {
            return ["monthlytotals-$teamId"];
        }

        return ["monthlytotals-{$teamId}-{$date->format('Ym')}"];
    }

    public function flushCache(int $teamId, Carbon $date): void
    {
        Cache::tags($this->getCacheTags($teamId, $date))->flush();
        Cache::tags($this->getCacheTags($teamId))->flush();
    }
}
