<?php

namespace App\Providers;

use App\Models\AccountPayable;
use App\Observers\AccountPayableObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        AccountPayable::observe(AccountPayableObserver::class);
    }
}
