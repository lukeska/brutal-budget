<?php

namespace App\Http\Controllers;

use App\Data\CategoryRequest;
use App\Data\ExpenseData;
use App\Data\ExpenseRequest;
use App\Data\ExpensesIndexPage;
use App\Models\Expense;
use App\Repositories\ExpensesRepository;
use App\Repositories\MonthlyTotalsRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    public function __construct(
        protected MonthlyTotalsRepository $monthlyTotalsRepository,
        protected ExpensesRepository $expensesRepository,
    ) {
    }

    public function index($year = null, $month = null)
    {
        $expensesView = Request::get('view') == 'daily' ? 'daily' : 'categories';

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

        $expenses = ExpenseData::collection(
            $this->expensesRepository->getExpensesByMonth(Auth::user()->currentTeam->id, $currentDate, $regularExpenses)
        );

        return Inertia::render('Expenses/Index', new ExpensesIndexPage(
            expenses: $expenses,
            monthlyTotals: $this->monthlyTotalsRepository->getMonthlyTotals($year, $month, $regularExpenses),
            expensesView: $expensesView,
        ));
    }

    public function create()
    {
        try {
            $expense = ExpenseRequest::validateAndCreate(Request::all());
        } catch (ValidationException $e) {
            return redirect(route('expenses.index'))->withErrors($e->errors());
        }

        $this->expensesRepository->createMonthlyExpenses($expense, Auth::user());

        Request::session()->flash('message', 'Expense created correctly');

        return redirect(route('expenses.index'));
    }

    public function update(Expense $expense, ExpenseRequest $data)
    {
        $expenseData = [
            ...($data->except('months')->toArray()),
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
