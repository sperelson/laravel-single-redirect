<?php

namespace Perelson\SingleRedirect;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class SingleRedirectServiceProvider extends BaseServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/single-redirect.php', 'single-redirect'
        );
    }

    /**
     * Register the config for publishing
     *
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/single-redirect.php' => config_path('single-redirect.php'),
            ], 'single-redirect');
        }

        $kernel = $this->app->make(Kernel::class);
        $kernel->prependMiddleware(HandleSingleRedirect::class);
    }
}
