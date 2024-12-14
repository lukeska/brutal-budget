<?php

namespace App\Repositories;

use App\Data\ExpenseRequest;
use App\Data\ProjectTotalData;
use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelData\DataCollection;

class ExpensesRepository
{
    public function getByMonth(int $teamId, Carbon $date, int $currencyId, ?bool $regular = null): Collection
    {
        $key = "expenses-getByMonth-$teamId-{$date->format('Ym')}-$currencyId-$regular";
        $tags = $this->getCacheTags($teamId, $date);

        if (Cache::tags($tags)->has($key)) {
            $expenses = Cache::tags($tags)->get($key);
        } else {
            $expenses = Expense::query()
                ->select('expenses.*', DB::raw('amount * rate AS converted_amount'))
                ->leftJoin('currency_exchange_rates', function ($join) use ($currencyId) {
                    $join->on('expenses.currency_id', '=', 'currency_exchange_rates.from_currency_id')
                        ->where('currency_exchange_rates.to_currency_id', '=', $currencyId);
                })
                ->where('team_id', $teamId)
                ->when($regular !== null, function ($query) use ($regular) {
                    return $query->where('is_regular', $regular);
                })
                ->month($date)
                ->with('currency')
                ->with('category')
                ->with('project')
                ->with('user')
                ->orderByDesc('date')
                ->orderByDesc('id')
                ->get();

            Cache::tags($tags)->put($key, $expenses);
        }

        return $expenses;
    }

    public function getByProject(int $teamId, int $projectId, int $currencyId): Collection
    {
        $key = "expenses-getByProject-{$teamId}-{$projectId}-{$currencyId}";
        $tags = $this->getCacheTags($teamId);

        if (Cache::tags($tags)->has($key)) {
            $expenses = Cache::tags($tags)->get($key);
        } else {
            $expenses = Expense::query()
                ->select('expenses.*', DB::raw('amount * rate AS converted_amount'))
                ->leftJoin('currency_exchange_rates', function ($join) use ($currencyId) {
                    $join->on('expenses.currency_id', '=', 'currency_exchange_rates.from_currency_id')
                        ->where('currency_exchange_rates.to_currency_id', '=', $currencyId);
                })
                ->where('team_id', $teamId)
                ->where('project_id', $projectId)
                ->with('currency')
                ->with('category')
                ->with('project')
                ->with('user')
                ->orderByDesc('date')
                ->orderByDesc('id')
                ->get();

            Cache::tags($tags)->put($key, $expenses);
        }

        return $expenses;
    }

    public function createMonthlyExpenses(ExpenseRequest $expense, ?User $user): Collection
    {
        $expenses = collect();

        foreach ($this->getMonthlyAmounts($expense->amount, $expense->months) as $index => $amount) {
            $expenseArray = $expense->except('months')->toArray();
            $expenseArray['amount'] = $amount;
            $expenseArray['date'] = Carbon::parse($expenseArray['date'])->addMonthsWithoutOverflow($index);

            $expenses->add(Expense::create([
                ...($expenseArray),
                'user_id' => $user->getAuthIdentifier(),
                'team_id' => $user->currentTeam->id,
            ]));
        }

        return $expenses;
    }

    public function getProjectsTotals(int $teamId, int $currencyId): DataCollection
    {
        $key = "expenses-getProjectsTotals-{$teamId}-{$currencyId}";
        $tags = $this->getCacheTags($teamId);

        if (Cache::tags($tags)->has($key)) {
            $projectTotals = Cache::tags($tags)->get($key);
        } else {
            $projectTotalsRaw = Expense::query()
                ->selectRaw('project_id, SUM(amount * rate) as total')
                ->leftJoin('currency_exchange_rates', function ($join) use ($currencyId) {
                    $join->on('expenses.currency_id', '=', 'currency_exchange_rates.from_currency_id')
                        ->where('currency_exchange_rates.to_currency_id', '=', $currencyId);
                })
                ->where('team_id', $teamId)
                ->whereNotNull('project_id')
                ->groupBy('project_id')
                ->get();

            $projectTotals = ProjectTotalData::collection($projectTotalsRaw->toArray());
        }

        return $projectTotals;
    }

    public function getCacheTags(int $teamId, ?Carbon $date = null): array
    {
        if ($date === null) {
            return ["expenses-$teamId"];
        }

        return ["expenses-{$teamId}-{$date->format('Ym')}"];
    }

    public function flushCache(int $teamId, Carbon $date): void
    {
        Cache::tags($this->getCacheTags($teamId, $date))->flush();
        Cache::tags($this->getCacheTags($teamId))->flush();
    }

    private function getMonthlyAmounts(int $amount, int $months): array
    {
        // Ensure the divisor is greater than 0 to avoid division by zero
        if ($months <= 0) {
            throw new \InvalidArgumentException('Divisor must be greater than 0.');
        }

        // Calculate the quotient and remainder
        $quotient = intdiv($amount, $months);
        $remainder = $amount % $months;

        // Initialize an array to store the distribution
        $distribution = array_fill(0, $months, $quotient);

        // Distribute the remainder
        for ($i = 0; $i < $remainder; $i++) {
            $distribution[$i]++;
        }

        return $distribution;
    }
}
