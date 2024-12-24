<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\OnboardingStatusController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\UserPushSettingsController;
use App\Http\Controllers\UserSettingsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('temp', function () {
    $rates = \App\Models\CurrencyExchangeRate::all();
    foreach ($rates as $rate) {
        echo "['id' => {$rate->id}, 'from_currency_id' => {$rate->from_currency_id}, 'to_currency_id' => {$rate->to_currency_id}, 'rate' => '{$rate->rate}'],<br>";
    }
});
Route::get('/', function () {
    return redirect()->to(route('dashboard'));
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard/{year?}/{month?}', [DashboardController::class, 'index'])
        ->where([
            'year' => '[0-9]{4}', // Year should be four digits
            'month' => '0[1-9]|1[0-2]', // Month should be 01 to 12
        ])->name('dashboard');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::patch('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'delete'])->name('categories.delete');
    Route::put('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories/{category}/icon', [CategoryController::class, 'updateIcon'])->name('categories.update-icon');

    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::patch('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'delete'])->name('projects.delete');
    Route::put('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects/{project}/hex', [ProjectController::class, 'updateHex'])->name('projects.update-hex');

    Route::get('/expenses/{year?}/{month?}', [ExpenseController::class, 'index'])
        ->where([
            'year' => '[0-9]{4}', // Year should be four digits
            'month' => '0[1-9]|1[0-2]', // Month should be 01 to 12
        ])
        ->name('expenses.index');
    Route::put('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::patch('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'delete'])->name('expenses.delete');

    Route::get('/expense/{expense}', [ExpenseController::class, 'show'])->name('expense.show');

    Route::get('/user/settings', [UserSettingsController::class, 'show'])->name('user-settings.show');
    Route::patch('/user/settings', [UserSettingsController::class, 'update'])->name('user-settings.update');
    Route::patch('/user/settings/toggle-currency', [UserSettingsController::class, 'toggleCurrency'])->name('user-settings.toggle-currency');

    Route::post('user/settings/push-subscribed', [UserPushSettingsController::class, 'subscribed'])->name('user-push-settings.subscribed');
    Route::post('user/settings/push-subscribe', [UserPushSettingsController::class, 'subscribe'])->name('user-push-settings.subscribe');
    Route::delete('user/settings/push-unsubscribe', [UserPushSettingsController::class, 'unsubscribe'])->name('user-push-settings.unsubscribe');
    Route::post('user/settings/push-test', [UserPushSettingsController::class, 'pushTest'])->name('user-push-settings.push-test');

    Route::patch('user/onboarding/{onboardingStatus}/skip', [OnboardingStatusController::class, 'skip'])->name('onboarding-status.skip');

    Route::get('/statistics/{year?}', [StatisticController::class, 'index'])
        ->where([
            'year' => '[0-9]{4}', // Year should be four digits
        ])->name('statistics.index');
});
