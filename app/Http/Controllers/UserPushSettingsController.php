<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Notifications\ExpenseCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class UserPushSettingsController extends Controller
{
    public function subscribe(Request $request)
    {
        Auth::user()->updatePushSubscription(
            endpoint: $request->get('endpoint'),
            key: $request->get('key'),
            token: $request->get('token'),
        );

        return back();
    }

    public function unsubscribe(Request $request)
    {
        Auth::user()->deletePushSubscription(
            endpoint: $request->get('endpoint'),
        );

        return back();
    }

    public function pushTest()
    {
        Auth::user()->notify(new ExpenseCreated(Expense::find(1)));

        return back();
    }
}
