<?php

namespace App\Http\Controllers;

use App\Data\OnboardingStatusData;
use App\Repositories\MonthlyTotalsRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(protected MonthlyTotalsRepository $monthlyTotalsRepository)
    {
    }

    public function index($year = null, $month = null)
    {
        $currentDate = Carbon::now();

        if ($year && $month) {
            $currentDate = Carbon::create($year, $month, 1);
        }

        return Inertia::render('Dashboard/Index', [
            'monthlyTotals' => $this->monthlyTotalsRepository->getAll(
                teamId: Auth::user()->currentTeam->id,
                year: $currentDate->year,
                month: $currentDate->month,
                previousMonthsOffset: 4,
                followingMonthsOffset: 0
            ),
            'month' => $currentDate->month,
            'year' => $currentDate->year,
            'onboardingStatuses' => OnboardingStatusData::collection(Auth::user()->onboardingStatuses),
        ]);
    }
}
