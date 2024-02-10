<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void generateByCategory(int $categoryId, int $teamId, int $yearMonth)
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
