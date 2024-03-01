<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Notifications\ExpenseCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPushSettingsController extends Controller
{
    public function subscribed(Request $request)
    {
        $subscription = app(config('webpush.model'))->findByEndpoint($request->input('endpoint'));

        $isSubscribed = Auth::user()->ownsPushSubscription($subscription);

        return [
            'subscribed' => $isSubscribed,
        ];
    }

    public function subscribe(Request $request)
    {
        Auth::user()->updatePushSubscription(
            endpoint: $request->input('endpoint'),
            key: $request->input('key'),
            token: $request->input('token'),
        );

        return back();
    }

    public function unsubscribe(Request $request)
    {
        Auth::user()->deletePushSubscription(
            endpoint: $request->input('endpoint'),
        );

        return back();
    }

    public function pushTest()
    {
        Auth::user()->notify(new ExpenseCreated(Expense::find(1)));

        return back();
    }
}
