<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use App\Observers\CategoryObserver;
use App\Observers\ExpenseObserver;
use App\Observers\ProjectObserver;
use App\Observers\TeamObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * The model observers for your application.
     *
     * @var array
     */
    protected $observers = [
        Team::class => [TeamObserver::class],
        Expense::class => [ExpenseObserver::class],
        Category::class => [CategoryObserver::class],
        Project::class => [ProjectObserver::class],
        User::class => [UserObserver::class],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
