<?php

namespace App\Providers;

use App\Courier\CourierGateway;
use App\Courier\DPD\DPDCourierGateway;
use App\Courier\GLS\GLSCourierGateway;
use App\Courier\PostisGate\PostisGateCourierGateway;
use App\Courier\UrgentCargus\UrgentCargusCourierGateway;
use App\Courier\TwoShip\TwoShipCourierGateway;
use Illuminate\Support\ServiceProvider;

class CourierServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CourierGateway::class, function($app, $array) {
            switch ($array['type']) {
                case 1:
                    return new UrgentCargusCourierGateway($array);
                case 2:
                    return new DPDCourierGateway($array);
                case 3:
                    return new GLSCourierGateway($array);
                case 4:
                    return new PostisGateCourierGateway($array);
                case 5:
                    return new TwoShipCourierGateway($array);
                default:
                    return true;
            }
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
