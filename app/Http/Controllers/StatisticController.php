<?php

namespace App\Http\Controllers;

use App\Repositories\MonthlyTotalsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class StatisticController extends Controller
{
    public function __construct(protected MonthlyTotalsRepository $monthlyTotalsRepository)
    {
    }

    public function index($year = null)
    {
        $currentDate = Carbon::now();

        if ($year) {
            $currentDate = Carbon::create($year, 12, 31);
        }

        $monthlyTotals = $this->monthlyTotalsRepository->getAll(
            teamId: Auth::user()->currentTeam->id,
            currencyId: Auth::user()->currency_id,
            year: $currentDate->year,
            month: $currentDate->month,
            previousMonthsOffset: 11,
            followingMonthsOffset: 0
        );

        return Inertia::render('Statistics/Index', [
            'monthlyTotals' => $monthlyTotals,
            'month' => $currentDate->month,
            'year' => $currentDate->year,
            'total' => collect($monthlyTotals)->sum('total'),
        ]);
    }
}
