<?php

namespace App\Providers;

use App\Helpers\Totals;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class BrutalBudgetServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('totals', function () {
            return new Totals();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Validator::extend('comma_decimal_positive', function ($attribute, $value, $parameters, $validator) {
            // Check if the value is numeric after replacing commas with dots
            $newValue = str_replace(',', '.', $value);
            return is_numeric($newValue) && $newValue > 0;
        });

        Validator::replacer('comma_decimal_positive', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, 'The :attribute must be a valid positive number with a comma as the decimal separator.');
        });
    }
}
