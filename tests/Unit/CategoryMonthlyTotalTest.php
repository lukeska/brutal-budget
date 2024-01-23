<?php

namespace Tests\Unit;

use App\Models\CategoryMonthlyTotal;
use App\Models\Expense;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** @group brutal */
class CategoryMonthlyTotalTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_calculate_monthly_totals()
    {
        /** @var User $luca */
        $luca = $this->signIn();

        /** @var Team $team */
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

        // 2 totals where is_regular is null. These are the totals that take into consideration both regular and non regular
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
    }

    /** @test */
    public function when_an_expense_month_changes_total_for_old_and_new_month_are_calculated()
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
    }
}
