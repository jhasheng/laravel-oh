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
    }
}