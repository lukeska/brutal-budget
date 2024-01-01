<?php

namespace App\Http\Controllers;

use App\Data\UserSettingsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class UserSettingsController extends Controller
{
    public function show(Request $request): Response
    {
        return Inertia::render('Settings/Show');
    }

    public function update(UserSettingsRequest $data)
    {
        Auth::user()->update($data->toArray());

        return back();
    }
}
