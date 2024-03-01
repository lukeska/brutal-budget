<?php

namespace App\Providers;

use Database\Providers\ExpenseProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class FakerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //if (App::environment('testing')) {
        $this->app->singleton(\Faker\Generator::class, function () {
            $faker = \Faker\Factory::create();
            $faker->addProvider(new ExpenseProvider($faker));

            return $faker;
        });
        //}
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
