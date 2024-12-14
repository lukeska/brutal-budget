<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static generateByCategory(int $categoryId, int $teamId, int $currencyId, int $yearMonth): void
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
