<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootSapoSocialite();
    }

    private function bootSapoSocialite()
    {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend('sapo', function ($app) use ($socialite) {
            $config = $app['config']['services.sapo'];
            return $socialite->buildProvider(SapoProvider::class, $config);
        });
    }
}
