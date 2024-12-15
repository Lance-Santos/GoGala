<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (env(key: 'APP_ENV') !== 'local') {
            URL::forceScheme(scheme: 'https');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(\Illuminate\Http\Request $request)
    {
        // if ($request->server->has('HTTP_X_ORIGINAL_HOST')) {
        //     $this->app['url']->forceRootUrl(env('NGROK_URL'));
        //     $request->server->set('HTTP_HOST', $request->server->get('HTTP_X_ORIGINAL_HOST'));
        //     $request->headers->set('HOST', $request->server->get('HTTP_X_ORIGINAL_HOST'));
        // }
    }
}
