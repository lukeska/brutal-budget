<?php

namespace App\Repositories;

use App\Data\ExpenseRequest;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ExpensesRepository
{
    public function getExpensesByMonth(int $teamId, Carbon $date, ?bool $regular = null): Collection
    {
        $key = `getExpensesByMonth:{$teamId}:{$date->format('Ym')}:{$regular}`;
        $tags = $this->getCacheTags($teamId, $date);

        if (Cache::tags($tags)->has($key)) {
            $expenses = Cache::tags($tags)->get($key);
        } else {
            $expenses = Expense::query()
                ->where('team_id', $teamId)
                ->when($regular !== null, function ($query) use ($regular) {
                    return $query->where('is_regular', $regular);
                })
                ->month($date)
                ->with('category')
                ->with('project')
                ->orderByDesc('date')
                ->orderByDesc('id')
                ->get();

            Cache::tags($tags)->put($key, $expenses);
        }

        return $expenses;
    }

    public function createMonthlyExpenses(ExpenseRequest $expense, ?\Illuminate\Contracts\Auth\Authenticatable $user): void
    {
        foreach ($this->getMonthlyAmounts($expense->amount, $expense->months) as $index => $amount) {
            $expenseArray = $expense->except('months')->toArray();
            $expenseArray['amount'] = $amount;
            $expenseArray['date'] = Carbon::parse($expenseArray['date'])->addMonthsWithoutOverflow($index);

            Expense::create([
                ...($expenseArray),
                'user_id' => $user->getAuthIdentifier(),
                'team_id' => $user->currentTeam->id,
            ]);
        }
    }

    public function getCacheTags(int $teamId, Carbon $date): array
    {
        return [`expenses:{$teamId}:{$date->format('Ym')}`];
    }

    public function flushCache(int $teamId, Carbon $date): void
    {
        Cache::tags($this->getCacheTags($teamId, $date))->flush();
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
