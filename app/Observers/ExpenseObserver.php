<?php

namespace App\Observers;

use App\Facades\Totals;
use App\Models\Expense;
use Carbon\Carbon;

class ExpenseObserver
{
    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense): void
    {
        Totals::generateByCategory($expense->category->id, $expense->team->id, (int) ((new Carbon($expense->date))->format('Ym')));
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        // date was changed, must update total relate to past date
        if ($expense->wasChanged('date')) {

            $originalDate = new Carbon($expense->getOriginal('date'));

            if ($originalDate->format('Ym') != $expense->date->format('Yd')) {
                Totals::generateByCategory($expense->category->id, $expense->team->id, $originalDate->format('Ym'));
            }
        }

        Totals::generateByCategory($expense->category->id, $expense->team->id, $expense->date->format('Ym'));
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        Totals::generateByCategory($expense->category->id, $expense->team->id, (int) ((new Carbon($expense->date))->format('Ym')));
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
}
