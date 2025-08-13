<?php

namespace App\Providers;

use App\Billing\PaymentGateway;
use App\Billing\Mobilpay\MobilpayPaymentGateway;
use App\Billing\LibraBank\LibraBankGateway;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PaymentGateway::class, function($app, $array) {
            if(isset($array['type'])) {
                switch ($array['type']) {
                    case 1:
                        return new MobilpayPaymentGateway($array);
                    case 2:
                        return new LibraBankGateway($array);
                    default:
            	        return new MobilpayPaymentGateway($array);
                }
            }
            return new MobilpayPaymentGateway($array);
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
