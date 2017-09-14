<?php

namespace Purple\OpensslHelper;

use Illuminate\Support\ServiceProvider;
use Purple\OpensslHelper\Commands\OHCACommand;

class OpensslHelperServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([OHCACommand::class]);
        }

        $this->publishes([
            __DIR__ . '/Config/openssl.php' => config_path('openssl.php')
        ]);
    }
}
