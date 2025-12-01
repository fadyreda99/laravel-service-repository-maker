<?php

namespace Fadyreda99\MakeModule\Commands;

use Illuminate\Console\Command;

class MakeModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module {path} {--model=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Service and Repository at the specified path, with optional model for full CRUD';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = $this->argument('path');
        $model = $this->option('model');

        $this->call('make:service', [
            'path' => $path . 'Service',
            '--model' => $model
        ]);

        // Run repository command
        $this->call('make:repository', [
            'path' => $path . 'Repository',
            '--model' => $model
        ]);

        $this->info("Module created: Service + Repository for ($path)" . ($model ? " with model $model" : ""));
    }
}
