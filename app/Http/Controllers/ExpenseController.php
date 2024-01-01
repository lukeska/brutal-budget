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
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    public function index($year = null, $month = null)
    {
        $regularExpenses = null;
        if (Request::has('type')) {
            $regularExpenses = Request::get('type') == 'regular'
                                ? true
                                : ((Request::get('type') == 'non-regular')
                                    ? false
                                    : null);
        }

        $currentDate = Carbon::now();

        if ($year && $month) {
            $currentDate = Carbon::create($year, $month, 1);
        }

        $expenses = ExpenseData::collection(Auth::user()
            ->currentTeam
            ->expenses()
            ->when($regularExpenses !== null, function ($query) use ($regularExpenses) {
                return $query->where('is_regular', $regularExpenses);
            })
            ->month($currentDate)
            ->with('category')
            ->with('project')
            ->orderByDesc('date')
            ->get());

        $rawTotals = CategoryMonthlyTotal::query()
            ->where('team_id', Auth::user()->currentTeam->id)
            ->where('year_month', $currentDate->format('Ym'))
            ->whereNull('user_id')
            ->where('is_regular', $regularExpenses)
            ->with('category')
            ->orderByDesc('amount')
            ->get();

        $totalExpenses = $rawTotals->sum('amount');

        $categoryMonthlyTotals = CategoryMonthlyTotalData::collection($rawTotals);

        $rawTotalsPreviousMonth = CategoryMonthlyTotal::where('team_id', Auth::user()->currentTeam->id)
            ->where('year_month', $currentDate->copy()->subMonth()->format('Ym'))
            ->whereNull('user_id')
            ->where('is_regular', $regularExpenses)
            ->with('category')
            ->orderByDesc('amount')
            ->get();

        $totalExpensesPreviousMonth = $rawTotalsPreviousMonth->sum('amount');

        $categoryMonthlyTotalsPreviousMonth = CategoryMonthlyTotalData::collection($rawTotalsPreviousMonth);

        $rawTotalsFollowingMonth = CategoryMonthlyTotal::where('team_id', Auth::user()->currentTeam->id)
            ->where('year_month', $currentDate->copy()->addMonth()->format('Ym'))
            ->whereNull('user_id')
            ->where('is_regular', $regularExpenses)
            ->with('category')
            ->orderByDesc('amount')
            ->get();

        $totalExpensesFollowingMonth = $rawTotalsFollowingMonth->sum('amount');

        $categoryMonthlyTotalsFollowingMonth = CategoryMonthlyTotalData::collection($rawTotalsFollowingMonth);

        return Inertia::render('Expenses/Index', new ExpensesIndexPage(
            expenses: $expenses,
            categoryMonthlyTotals: $categoryMonthlyTotals,
            categoryMonthlyTotalsPreviousMonth: $categoryMonthlyTotalsPreviousMonth,
            categoryMonthlyTotalsFollowingMonth: $categoryMonthlyTotalsFollowingMonth,
            totalExpenses: $totalExpenses,
            totalExpensesPreviousMonth: $totalExpensesPreviousMonth,
            totalExpensesFollowingMonth: $totalExpensesFollowingMonth,
            year: $currentDate->year,
            month: $currentDate->month
        ));
    }

    public function create()
    {
        $expense = ExpenseRequest::validate(Request::all());

        Auth::user()->currentTeam->expenses()->create([...$expense, 'user_id' => Auth::user()->getAuthIdentifier()]);

        Request::session()->flash('message', 'Expense created correctly');

        return back();
    }

    public function update(Expense $expense, ExpenseRequest $data)
    {
        // TODO: add policy here?

        $expenseData = [
            ...$data->all(),
            'user_id' => Auth::user()->getAuthIdentifier(),
        ];

        $expense->update($expenseData);

        Request::session()->flash('message', 'Expense updated correctly');

        return back();
    }

    public function delete(Expense $expense)
    {
        /*if (Auth::user()->cannot('delete', $expense)) {
            abort(403);
        }*/

        $expense->delete();

        Request::session()->flash('message', 'Expense deleted correctly');

        return back();
    }
}
