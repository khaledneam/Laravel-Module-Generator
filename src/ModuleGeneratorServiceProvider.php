<?php

namespace khaledneam\ModuleGenerator;

use Illuminate\Support\ServiceProvider;

class ModuleGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        // Register the command
        $this->commands([
            Commands\MakeModuleCommand::class,
        ]);
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../Modules/Blog/Views', 'blog');
    }
}
