<?php

namespace App\Http\Controllers;

use App\Models\OnboardingStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OnboardingStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(OnboardingStatus $onboardingStatus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OnboardingStatus $onboardingStatus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OnboardingStatus $onboardingStatus)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OnboardingStatus $onboardingStatus)
    {
        //
    }

    public function skip(OnboardingStatus $onboardingStatus)
    {
        $onboardingStatus->update([
            'skipped_at' => now()
        ]);

        return back();
    }
}
