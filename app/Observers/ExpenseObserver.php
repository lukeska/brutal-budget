<?php

namespace App\Observers;

use App\Events\ExpenseCreated;
use App\Events\ExpenseDeleted;
use App\Events\ExpenseUpdated;
use App\Facades\Totals;
use App\Models\Expense;
use App\Notifications\ExpenseCreated as ExpenseCreatedNotification;
use App\Repositories\ExpensesRepository;
use App\Repositories\MonthlyTotalsRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

class ExpenseObserver
{
    public function __construct(
        protected ExpensesRepository $expensesRepository,
        protected MonthlyTotalsRepository $monthlyTotalsRepository,
    ) {
    }

    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense): void
    {
        $this->flushCache($expense->team_id, Carbon::create($expense->date));

        Totals::generateByCategory($expense->category->id, $expense->team->id, (int) ((Carbon::create($expense->date))->format('Ym')));

        // broadcast event for Laravel Echo to pick up
        broadcast(new ExpenseCreated($expense))->toOthers();

        // send notifications
        $users = $expense->team->allUsers()->reject(function ($user) use ($expense) {
            return $user->id === $expense->user->id;
        });

        Notification::send($users, new ExpenseCreatedNotification($expense));
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        $newDate = new Carbon($expense->date);

        $this->expensesRepository->flushCache($expense->team_id, $newDate);

        Totals::generateByCategory($expense->category->id, $expense->team->id, $newDate->format('Ym'));

        // date was changed, must update total relate to past date
        if ($expense->wasChanged('date')) {

            $originalDate = new Carbon($expense->getOriginal('date'));

            if ($originalDate->format('Ym') != $newDate->format('Yd')) {
                $this->flushCache($expense->team_id, $originalDate);
                Totals::generateByCategory($expense->category->id, $expense->team->id, $originalDate->format('Ym'));
            }
        }

        // broadcast event for Laravel Echo to pick up
        broadcast(new ExpenseUpdated($expense))->toOthers();
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        $this->flushCache($expense->team_id, new Carbon($expense->date));

        Totals::generateByCategory($expense->category->id, $expense->team->id, (int) ((new Carbon($expense->date))->format('Ym')));

        // broadcast event for Laravel Echo to pick up
        broadcast(new ExpenseDeleted($expense->id, $expense->team->id))->toOthers();
    }

    /**
     * Handle the Expense "restored" event.
     */
    public function restored(Expense $expense): void
    {
        //
    }

    /**
     * Handle the Expense "force deleted" event.
     */
    public function forceDeleted(Expense $expense): void
    {
        //
    }

    protected function flushCache(int $teamId, Carbon $date): void
    {
        $this->expensesRepository->flushCache($teamId, $date);
        $this->monthlyTotalsRepository->flushCache($teamId, $date);
    }
}
