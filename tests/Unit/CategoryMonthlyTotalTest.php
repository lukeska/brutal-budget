<?php

namespace Tests\Unit;

use App\Models\CategoryMonthlyTotal;
use App\Models\Expense;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

/** @group Brutal */
class CategoryMonthlyTotalTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_calculate_monthly_totals()
    {
        /** @var User $luca */
        $luca = $this->signIn();

        /** @var Team $team */
        $team = $luca->currentTeam;

        $category1 = $team->categories->first();
        $category2 = $team->categories->last();

        $viola = User::factory()->withPersonalTeam()->create();
        $team->users()->attach($viola, ['role' => 'admin']);

        Expense::factory(5)->create([
            'user_id' => $luca->id,
            'category_id' => $category1->id,
            'team_id' => $team->id,
            'date' => Carbon::now(),
            'amount' => 100,
        ]);

        Expense::factory(2)->create([
            'user_id' => $viola->id,
            'category_id' => $category1->id,
            'team_id' => $team->id,
            'date' => Carbon::now(),
            'amount' => 50,
        ]);

        Expense::factory(10)->create([
            'user_id' => $luca->id,
            'category_id' => $category2->id,
            'team_id' => $team->id,
            'date' => Carbon::now(),
            'amount' => 10,
        ]);

        $this->assertCount(2, CategoryMonthlyTotal::all());

        $yearMonth = Carbon::now()->format('Ym');

        $groupTotal = CategoryMonthlyTotal::where('year_month', $yearMonth)
            ->where('category_id', $category1->id)
            ->where('team_id', $team->id)
            ->whereNull('user_id')
            ->first();

        $this->assertEquals(600, $groupTotal->amount);

        $groupTotal2 = CategoryMonthlyTotal::where('year_month', $yearMonth)
            ->where('category_id', $category2->id)
            ->where('team_id', $team->id)
            ->whereNull('user_id')
            ->first();

        $this->assertEquals(100, $groupTotal2->amount);
    }

    /** @test */
    function when_an_expense_month_changes_total_for_old_and_new_month_are_calculated()
    {
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
            ->get();

        $groupTotalNew = CategoryMonthlyTotal::where('year_month', $nextMonth->format('Ym'))
            ->where('category_id', $category1->id)
            ->where('team_id', $team->id)
            ->whereNull('user_id')
            ->get();

        $this->assertCount(0, $groupTotalOld);
        $this->assertCount(1, $groupTotalNew);

        $this->assertEquals(100, $groupTotalNew->first()->amount);
    }
}
