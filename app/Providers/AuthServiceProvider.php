<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\Cadastro; // Importe o modelo Cadastro

class AuthServiceProvider extends ServiceProvider
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
        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->header('Authorization')) {
                return Cadastro::where('api_token', $request->header('Authorization'))->first();
            }
        });
    }
}
