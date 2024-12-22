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
        if($data->currencyId == $data->secondaryCurrencyId) {
            $data->secondaryCurrencyId = null;
        }

        Auth::user()->update($data->toArray());

        return back();
    }

    public function toggleCurrency()
    {
        $user = Auth::user();

        if($user->secondary_currency_id) {
            $user->update([
                'currency_id' => $user->secondary_currency_id,
                'secondary_currency_id' => $user->currency_id
            ]);
        }

        return back();
    }
}
