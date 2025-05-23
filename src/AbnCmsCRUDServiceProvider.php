<?php

namespace Aman5537jains\AbnCmsCRUD;


use Illuminate\Support\ServiceProvider;
use Aman5537jains\AbnCmsCRUD\Commands\AbnCrud;
class AbnCmsCRUDServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/crud.php' => config_path('crud.php'),
        ],'config');
        
        // $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        // $this->loadMigrationsFrom(__DIR__.'/migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'AbnCmsCrud');

        \Route::get("/component-render",function(){
            return CrudService::renderComponent();

        })->name("component-render");
        $this->publishes([
            __DIR__.'/resources/assets' => public_path('vendor/abncrud'),
        ], 'assets');
        // $this->loadMigrationsFrom(__DIR__.'/migrations');
        // $this->publishes([
        //     __DIR__.'/views' => base_path('resources/views/aman'),
        // ]);

    }




    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            AbnCrud::class,
        ]);
        // $this->mergeConfigFrom(
        //     __DIR__.'/abncms.php',
        //     'abncms'
        // );
    }
}
