<?php

namespace App\Providers;

use App\Models\Activity;
use App\Models\BudgetItem;
use App\Models\Guest;
use App\Models\Payment;
use App\Models\SeserahanItem;
use App\Models\Vendor;
use App\Observers\ActivityObserver;
use App\Observers\BudgetItemObserver;
use App\Observers\GuestObserver;
use App\Observers\PaymentObserver;
use App\Observers\SeserahanItemObserver;
use App\Observers\VendorObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Activity log (Fase 3) — observer diregistrasi eksplisit
        Activity::observe(ActivityObserver::class);
        BudgetItem::observe(BudgetItemObserver::class);
        Payment::observe(PaymentObserver::class);
        Vendor::observe(VendorObserver::class);
        SeserahanItem::observe(SeserahanItemObserver::class);
        Guest::observe(GuestObserver::class);
    }
}
