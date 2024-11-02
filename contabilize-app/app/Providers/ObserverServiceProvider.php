<?php

namespace App\Providers;

use App\Models\AccountPayable;
use App\Models\CreditCard;
use App\Models\CreditCardPurchase;
use App\Models\PurchaseInstallment;
use App\Observers\AccountPayableObserver;
use App\Observers\CreditCardPurchaseObserver;
use App\Observers\PurchaseInstallmentObserver;
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
        CreditCardPurchase::observe(CreditCardPurchaseObserver::class);
        PurchaseInstallment::observe(PurchaseInstallmentObserver::class);
    }
}
