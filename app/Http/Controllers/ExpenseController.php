<?php

namespace App\Http\Controllers;

use App\Data\ExpenseData;
use App\Data\ExpenseRequest;
use App\Data\ExpensesIndexPage;
use App\Models\Expense;
use App\Repositories\MonthlyTotalsRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    public function __construct(
        protected MonthlyTotalsRepository $monthlyTotalsRepository
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

        return Inertia::render('Expenses/Index', new ExpensesIndexPage(
            expenses: $expenses,
            monthlyTotals: $this->monthlyTotalsRepository->getMonthlyTotals($year, $month, $regularExpenses),
            expensesView: $expensesView,
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
