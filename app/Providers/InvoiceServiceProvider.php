<?php

namespace App\Providers;

use App\Invoicing\InvoiceGateway;
use App\Invoicing\Oblio\OblioInvoiceGateway;
use Illuminate\Support\ServiceProvider;

class InvoiceServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(InvoiceGateway::class, function($app, $array = []) {
            return new OblioInvoiceGateway($array);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
