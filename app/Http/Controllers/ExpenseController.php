<?php

namespace App\Http\Controllers;

use App\Models\CategoryMonthlyTotal;
use App\Models\Expense;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    public function index()
    {
        $categoryMonthlyTotals = CategoryMonthlyTotal::where('team_id', Auth::user()->currentTeam->id)
            ->where('year_month', Carbon::now()->format('Ym'))
            ->whereNull('user_id')
            ->with('category')
            ->orderByDesc('amount')
            ->get();

        $expenses = Auth::user()
            ->currentTeam
            ->expenses()
            ->currentMonth()
            ->with('category')
            ->orderByDesc('created_at')
            ->get();

        $totalExpenses = $categoryMonthlyTotals->sum('amount');

        return Inertia::render('Expenses/Index', [
            'expenses' => $expenses,
            'categoryMonthlyTotals' => $categoryMonthlyTotals,
            'totalExpenses' => $totalExpenses
        ]);
    }

    public function create()
    {
        $attributes = Request::validate([
            'amount' => ['required',
                'min:0.01','comma_decimal'
            ],
            'date' => ['required', 'date'],
            'notes' => ['nullable'],
            'category_id' => ['required','exists:App\Models\Category,id'] // TODO: scope to categories for the current group
        ]);

        $attributes['date'] = Carbon::parse($attributes['date']);
        $attributes['amount'] = str_replace(',', '.', $attributes['amount']) * 100;

        Auth::user()->currentTeam->expenses()->create([...$attributes, 'user_id' => Auth::user()->getAuthIdentifier()]);

        Request::session()->flash('message', 'Expense created correctly');

        return redirect('/expenses');
    }

    public function update(Expense $expense)
    {
        $attributes = Request::validate([
            'amount' => ['required',
                'min:0.01','comma_decimal'
            ],
            'date' => ['required', 'date'],
            'notes' => ['nullable'],
            'category_id' => ['required','exists:App\Models\Category,id'] // TODO: scope to categories for the current group
        ]);

        $attributes['date'] = Carbon::parse($attributes['date']);
        $attributes['amount'] = str_replace(',', '.', $attributes['amount']) * 100;

        $expense->update($attributes);

        Request::session()->flash('message', 'Expense updated correctly');

        return redirect('/expenses');
    }

    public function delete(Expense $expense)
    {
        /*if (Auth::user()->cannot('delete', $expense)) {
            abort(403);
        }*/

        $expense->delete();

        Request::session()->flash('message', 'Expense deleted correctly');

        return redirect('/expenses');
    }
}
