<?php

namespace App\Repositories;

use App\Data\ExpenseRequest;
use App\Models\Expense;
use Carbon\Carbon;

class ExpensesRepository
{
    public function createMonthlyExpenses(ExpenseRequest $expense, ?\Illuminate\Contracts\Auth\Authenticatable $user)
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
