<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static generateByCategory(int $category_id, int $team_id, int $year_month): void
 *
 * @see \App\Helpers\Totals
 */
class Totals extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'totals';
    }
}
