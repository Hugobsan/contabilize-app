<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\AccountPayable;
use App\Models\AccountReceivable;
use App\Models\CreditCard;
use App\Models\CreditCardPurchase;
use App\Models\PurchaseInstallment;
use App\Policies\AccountPayablePolicy;
use App\Policies\AccountReceivablePolicy;
use App\Policies\CreditCardPolicy;
use App\Policies\CreditCardPurchasePolicy;
use App\Policies\PurchaseInstallmentPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        AccountPayable::class => AccountPayablePolicy::class,
        AccountReceivable::class => AccountReceivablePolicy::class,
        CreditCard::class => CreditCardPolicy::class,
        CreditCardPurchase::class => CreditCardPurchasePolicy::class,
        PurchaseInstallment::class => PurchaseInstallmentPolicy::class,
    ];

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
        $this->registerPolicies();
    }
}
