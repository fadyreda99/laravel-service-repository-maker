<?php


namespace Fadyreda99\MakeModule\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeService extends Command
{
    protected $signature = 'make:service {path} {--model=}';
    protected $description = 'Create a service class at the specified path with optional repository CRUD functions';

    public function handle()
    {
        $path = $this->argument('path');
        $model = $this->option('model');

        $fullPath = $this->generateFullPath($path);
        $directory = dirname($fullPath);

        $this->makeDirectory($directory);
        $this->makeFile($fullPath, $path, $model);
    }

    public function generateFullPath($path)
    {
        $baseDirectory = $this->laravel->path('Services');
        $fullPath = $baseDirectory . '/' . str_replace('\\', '/', $path) . '.php';
        return $fullPath;

        // return app_path('Services/' . str_replace('\\', '/', $path) . '.php');
    }

    public function makeDirectory($directory)
    {
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0777, true);
            $this->info("Directory created at: $directory");
        }
    }

    public function makeFile($fullPath, $path, $model)
    {
        if (File::exists($fullPath)) {
            return $this->error("Service already exists at: $fullPath");
        }

        $className = basename($path);
        $namespacePath = dirname($path);
        $namespace = 'App\\Services';
        if ($namespacePath !== '.') {
            $namespace .= '\\' . str_replace('/', '\\', $namespacePath);
        }

        // إذا فيه model → يطلع CRUD service مربوط بالـ repository
        if ($model) {
            $repositoryName = $model . 'Repository';
            $repositoryNamespace = "App\\Repositories";

            $methods = $this->generateServiceCrud($model);

            $template = "<?php

namespace $namespace;

use $repositoryNamespace\\$repositoryName;

class $className
{
    public function __construct(private $repositoryName \$repository) {}

$methods
}
";
        } else {
            // Service فاضي تمامًا
            $template = "<?php

namespace $namespace;

class $className
{
    // Service methods
}
";
        }

        File::put($fullPath, $template);
        $this->info("Service created at: $fullPath");
    }

    private function generateServiceCrud($model)
    {
        return <<<CRUD

    public function allWithConidtion(\$request)
    {
        \$data = \$request->all();
        \$condition = [];
        \$with = [];
        \$paginated = false;
        \$limit = 10;
        \$orderBy = [];
        return \$this->repository->allWithConidtion(\$condition, \$with, \$paginated, \$limit, \$orderBy);
    }

    public function find(\$request)
    {
        \$data = \$request->all();
        \$id = \$data['id'];
        \$condition = [];
        \$with = [];
        return \$this->repository->find(\$id, \$with, \$condition);
    }

    public function create(\$request)
    {
        \$data = \$request->all();
        return \$this->repository->create(\$data);
    }

    public function update(\$request)
    {
        \$data = \$request->all();
        \$id = \$data['id'];
        return \$this->repository->update(\$id, \$data);
    }

    public function delete(\$request)
    {
        \$data = \$request->all();
        \$id = \$data['id'];
        \$condition = [];
        return \$this->repository->delete(\$id, \$condition);
    }
CRUD;
    }
}
