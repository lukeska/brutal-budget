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
        Validator::extend('comma_decimal', function ($attribute, $value, $parameters, $validator) {
            // Check if the value is numeric after replacing commas with dots
            return is_numeric(str_replace(',', '.', $value));
        });

        Validator::replacer('comma_decimal', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, 'The :attribute must be a valid number with a comma as the decimal separator.');
        });
    }
}
