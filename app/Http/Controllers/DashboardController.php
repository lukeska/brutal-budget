<?php

namespace App\Http\Controllers;

use App\Repositories\MonthlyTotalsRepository;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(protected MonthlyTotalsRepository $monthlyTotalsRepository)
    {
    }

    public function index()
    {
        return Inertia::render('Dashboard', [
            'monthlyTotals' => $this->monthlyTotalsRepository->getAll(
                teamId: Auth::user()->current_team_id,
                year: now()->year,
                month: now()->month,
                previousMonthsOffset: 4,
                followingMonthsOffset: 0
            ),
        ]);
    }
}
