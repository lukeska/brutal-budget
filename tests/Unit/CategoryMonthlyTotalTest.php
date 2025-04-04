<?php

use App\Facades\Totals;
use App\Models\CategoryMonthlyTotal;
use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;

pest()->group('brutal');

test('it can calculate monthly totals', function () {
    $luca = $this->signIn();
    $team = $luca->currentTeam;
    $category1 = $team->categories->first();
    $category2 = $team->categories->last();

    $viola = User::factory()->withPersonalTeam()->create();
    $team->users()->attach($viola, ['role' => 'admin']);

    Expense::factory(5)->isRegular()->create([
        'user_id' => $luca->id,
        'category_id' => $category1->id,
        'team_id' => $team->id,
        'amount' => 100,
    ]);

    Expense::factory(2)->isNotRegular()->create([
        'user_id' => $viola->id,
        'category_id' => $category1->id,
        'team_id' => $team->id,
        'amount' => 50,
    ]);

    Expense::factory(10)->isRegular()->create([
        'user_id' => $luca->id,
        'category_id' => $category2->id,
        'team_id' => $team->id,
        'amount' => 10,
    ]);

    $this->assertCount(5, CategoryMonthlyTotal::all());
    $this->assertCount(2, CategoryMonthlyTotal::whereNull('is_regular')->get());
    $this->assertCount(2, CategoryMonthlyTotal::where('is_regular', true)->get());
    $this->assertCount(1, CategoryMonthlyTotal::where('is_regular', false)->get());

    $yearMonth = Carbon::now()->format('Ym');

    $groupTotal = CategoryMonthlyTotal::where('year_month', $yearMonth)
        ->where('category_id', $category1->id)
        ->where('team_id', $team->id)
        ->whereNull('user_id')
        ->whereNull('is_regular')
        ->first();

    $this->assertEquals(600, $groupTotal->amount);

    $groupTotalRegularExpenses = CategoryMonthlyTotal::where('year_month', $yearMonth)
        ->where('category_id', $category1->id)
        ->where('team_id', $team->id)
        ->whereNull('user_id')
        ->where('is_regular', true)
        ->first();

    $this->assertEquals(500, $groupTotalRegularExpenses->amount);

    $groupTotalNotRegularExpenses = CategoryMonthlyTotal::where('year_month', $yearMonth)
        ->where('category_id', $category1->id)
        ->where('team_id', $team->id)
        ->whereNull('user_id')
        ->where('is_regular', false)
        ->first();

    $this->assertEquals(100, $groupTotalNotRegularExpenses->amount);

    $groupTotal2 = CategoryMonthlyTotal::where('year_month', $yearMonth)
        ->where('category_id', $category2->id)
        ->where('team_id', $team->id)
        ->whereNull('user_id')
        ->whereNull('is_regular')
        ->first();

    $this->assertEquals(100, $groupTotal2->amount);
});

test('when an expense month changes total for old and new month are calculated', function () {
    $luca = $this->signIn();
    $team = $luca->currentTeam;
    $category1 = $team->categories->first();

    $today = Carbon::now();
    $nextMonth = Carbon::now()->add('1 month');

    $expense = Expense::factory()->create([
        'user_id' => $luca->id,
        'category_id' => $category1->id,
        'team_id' => $team->id,
        'date' => $today,
        'amount' => 100,
    ]);

    $expense->update([
        'date' => $nextMonth,
    ]);

    $groupTotalOld = CategoryMonthlyTotal::where('year_month', $today->format('Ym'))
        ->where('category_id', $category1->id)
        ->where('team_id', $team->id)
        ->whereNull('user_id')
        ->whereNull('is_regular')
        ->get();

    $groupTotalNew = CategoryMonthlyTotal::where('year_month', $nextMonth->format('Ym'))
        ->where('category_id', $category1->id)
        ->where('team_id', $team->id)
        ->whereNull('user_id')
        ->whereNull('is_regular')
        ->get();

    $this->assertCount(0, $groupTotalOld);
    $this->assertCount(1, $groupTotalNew);
    $this->assertEquals(100, $groupTotalNew->first()->amount);
});

test('it calculate totals for old and new categories when an expense category changes', function () {
    $luca = $this->signIn();
    $team = $luca->currentTeam;
    $category1 = $team->categories->first();
    $category2 = $team->categories->last();

    $expense = Expense::factory()->create([
        'user_id' => $luca->id,
        'category_id' => $category1->id,
        'team_id' => $team->id,
        'amount' => 100,
    ]);

    $groupTotalCategory1 = CategoryMonthlyTotal::query()
        ->where('category_id', $category1->id)
        ->where('team_id', $team->id)
        ->whereNull('user_id')
        ->whereNull('is_regular')
        ->get();

    $groupTotalCategory2 = CategoryMonthlyTotal::query()
        ->where('category_id', $category2->id)
        ->where('team_id', $team->id)
        ->whereNull('user_id')
        ->whereNull('is_regular')
        ->get();

    $this->assertCount(1, $groupTotalCategory1);
    $this->assertCount(0, $groupTotalCategory2);

    $expense->update([
        'category_id' => $category2->id,
    ]);

    $groupTotalCategory1 = CategoryMonthlyTotal::query()
        ->where('category_id', $category1->id)
        ->where('team_id', $team->id)
        ->whereNull('user_id')
        ->whereNull('is_regular')
        ->get();

    $groupTotalCategory2 = CategoryMonthlyTotal::query()
        ->where('category_id', $category2->id)
        ->where('team_id', $team->id)
        ->whereNull('user_id')
        ->whereNull('is_regular')
        ->get();

    $this->assertCount(0, $groupTotalCategory1);
    $this->assertCount(1, $groupTotalCategory2);
});

test('it can calculate totals for expenses in multiple currencies', function () {
    $this->travelTo('2024-01-01');
    $luca = $this->signIn();
    $team = $luca->currentTeam;
    $category = $team->categories->first();

    Expense::withoutEvents(function () use ($luca, $category, $team) {
        Expense::factory()->create([
            'user_id' => $luca->id,
            'category_id' => $category->id,
            'team_id' => $team->id,
            'amount' => 100,
            'currency_id' => 1,
        ]);

        Expense::factory()->create([
            'user_id' => $luca->id,
            'category_id' => $category->id,
            'team_id' => $team->id,
            'amount' => 100,
            'currency_id' => 2,
        ]);
    });

    Totals::generateByCategory($category->id, $team->id, 1, 202401);

    $categoryMonthlyTotal = CategoryMonthlyTotal::query()
        ->where('category_id', $category->id)
        ->where('team_id', $team->id)
        ->where('year_month', '202401')
        ->first();

    $this->assertEquals(193, $categoryMonthlyTotal->amount);
    $this->assertEquals(1, $categoryMonthlyTotal->currency_id);

    Totals::generateByCategory($category->id, $team->id, 2, 202401);

    $categoryMonthlyTotal = CategoryMonthlyTotal::query()
        ->where('category_id', $category->id)
        ->where('team_id', $team->id)
        ->where('year_month', '202401')
        ->first();

    $this->assertEquals(208, $categoryMonthlyTotal->amount);
    $this->assertEquals(2, $categoryMonthlyTotal->currency_id);
});
