<?php

namespace App\Http\Controllers;

use App\Data\CategoryMonthlyTotalData;
use App\Data\ExpenseData;
use App\Data\ExpenseRequest;
use App\Data\ExpensesIndexPage;
use App\Models\CategoryMonthlyTotal;
use App\Models\Expense;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    public function index()
    {
        $rawTotals = CategoryMonthlyTotal::where('team_id', Auth::user()->currentTeam->id)
            ->where('year_month', Carbon::now()->format('Ym'))
            ->whereNull('user_id')
            ->with('category')
            ->orderByDesc('amount')
            ->get();

        $totalExpenses = $rawTotals->sum('amount');

        $categoryMonthlyTotals = CategoryMonthlyTotalData::collection($rawTotals);

        $expenses = ExpenseData::collection(Auth::user()
            ->currentTeam
            ->expenses()
            ->currentMonth()
            ->with('category')
            ->orderByDesc('created_at')
            ->get());

        return Inertia::render('Expenses/Index', new ExpensesIndexPage(
            expenses: $expenses,
            categoryMonthlyTotals: $categoryMonthlyTotals,
            totalExpenses: $totalExpenses
        ));
    }

    public function create()
    {
        $expense = ExpenseRequest::validate(Request::all());

        $expense['amount'] = str_replace(',', '.', $expense['amount']) * 100;

        Auth::user()->currentTeam->expenses()->create([...$expense, 'user_id' => Auth::user()->getAuthIdentifier()]);

        Request::session()->flash('message', 'Expense created correctly');

        return redirect('/expenses');
    }

    public function update(Expense $expense, ExpenseRequest $data)
    {
        // TODO: add policy here?

        $expenseData = [
            ...$data->all(),
            'user_id' => Auth::user()->getAuthIdentifier()
        ];
        $expenseData['amount'] = str_replace(',', '.', $expenseData['amount']) * 100;

        $expense->update($expenseData);

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
