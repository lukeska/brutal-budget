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
        //dd(\Illuminate\Support\Facades\Request::all());
    }

    public function pushTest()
    {
        /*$webPush = new WebPush([
            'VAPID' => [
                'publicKey' => config('webpush.vapid.public_key'),
                'privateKey' => config('webpush.vapid.private_key'),
                'subject' => 'https://brutal-budget.test',
            ],
        ]);
        $webPush->sendOneNotification(
            Subscription::create()
        );*/
        Log::debug('push test');
        Auth::user()->notify(new ExpenseCreated(Expense::find(1)));

        return Inertia::render('Settings/Show');
    }
}
