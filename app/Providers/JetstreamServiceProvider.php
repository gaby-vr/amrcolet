<?php

namespace App\Providers;

use App\Actions\Jetstream\DeleteUser;
use App\Http\Livewire\Footer;
use App\Http\Livewire\Dashboard;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Compilers\BladeCompiler;
use Laravel\Jetstream\Jetstream;
use Livewire\Livewire;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (config('jetstream.stack') === 'livewire' && class_exists(Livewire::class)) {
            Livewire::component('footer', Footer::class);
            foreach ([
                'profile-menu', 'invoice', 'settings', 'addresses', 'purse',
                'templates', 'invoices', 'orders', 'repayments', 'financiar',
                'plugin'
            ] as $name) {
                Livewire::component($name, 'App\\Http\\Livewire\\Profile\\'.\Str::studly($name));
            }
        }
    }

    protected function registerComponent(string $component)
    {
        Blade::component('jetstream::components.'.$component, 'jet-'.$component);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePermissions();
        foreach ([
            'radio', 'select', 'table', 'tr', 'td', 'alert-card', 'dashboard',
            'admin-navigation', 'form-section-full', 'form-section-simple',
            'validation-messages', 'select', 'order-status', 'invoice-pill'
        ] as $name) {
            $this->registerComponent($name);
        }
        Jetstream::deleteUsersUsing(DeleteUser::class);
    }

    /**
     * Configure the permissions that are available within the application.
     *
     * @return void
     */
    protected function configurePermissions()
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::permissions([
            'create',
            'read',
            'update',
            'delete',
        ]);
    }
}
