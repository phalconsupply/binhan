<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Transaction;
use App\Models\SalaryAdvance;
use App\Observers\TransactionObserver;
use App\Observers\SalaryAdvanceObserver;

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
        Transaction::observe(TransactionObserver::class);
        SalaryAdvance::observe(SalaryAdvanceObserver::class);
    }
}
