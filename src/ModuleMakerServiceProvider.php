<?php

namespace Fadyreda99\MakeModule;

use Illuminate\Support\ServiceProvider;
use Fadyreda99\MakeModule\Commands\MakeService;
use Fadyreda99\MakeModule\Commands\MakeRepository;
use Fadyreda99\MakeModule\Commands\MakeModule as MakeModuleCommand;

class ModuleMakerServiceProvider extends ServiceProvider
{
    public function register()
    {
        // أي binding لو احتجته
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeService::class,
                MakeRepository::class,
                MakeModuleCommand::class,
            ]);

            // لو عايز تنشر ملفات (config, stubs...):
            // $this->publishes([
            //     __DIR__.'/../stubs' => base_path('stubs/vendor/make-module'),
            // ], 'make-module-stubs');
        }
    }
}
