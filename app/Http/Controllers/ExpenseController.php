<?php

namespace App\Http\Controllers;

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
            $this->expensesRepository->getByMonth(Auth::user()->currentTeam->id, $currentDate, $regularExpenses)
        );

        return Inertia::render('Expenses/Index', new ExpensesIndexPage(
            expenses: $expenses,
            monthlyTotals: $this->monthlyTotalsRepository->getAll(Auth::user()->currentTeam->id, $year, $month, $regularExpenses),
        ));
    }

    public function show(Expense $expense)
    {
        if (Request::user()->can('view', $expense)) {
            if (Request::user()->current_team_id !== $expense->team_id) {
                Request::user()->switchTeam($expense->team);
            }

            $expense->load('category')->load('user');

            Request::session()->flash('expense', ExpenseData::from($expense));
        }

        /*
         * Redirect to expenses index with the selected expense as a flash message.
         * The expense will be shown in the slide-in panel.
         * Not ideal as the url doesn't reflect which expense is visible.
         * Might review this solution in the future.
         */
        return to_route('expenses.index');
    }

    public function create()
    {
        try {
            $expense = ExpenseRequest::validateAndCreate(Request::all());
        } catch (ValidationException $e) {
            return redirect(route('expenses.index'))->withErrors($e->errors());
        }

        $expenses = $this->expensesRepository->createMonthlyExpenses($expense, Auth::user());

        Request::session()->flash('message', 'Expense created correctly');
        Request::session()->flash('expense', ExpenseData::from($expenses->first()));

        return back();
    }

    public function update(Expense $expense, ExpenseRequest $data)
    {
        if (! Request::user()->can('update', $expense)) {
            abort(403);
        }

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
        if (! Request::user()->can('delete', $expense)) {
            abort(403);
        }

        $expense->delete();

        Request::session()->flash('message', 'Expense deleted correctly');

        return back();
    }
}
