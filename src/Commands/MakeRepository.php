<?php

namespace Fadyreda99\MakeModule\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {path} {--model=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a repository class at the specified path';

    public function handle()
    {
        $path = $this->argument('path');
        $model = $this->option('model');

        $fullPath = $this->generateFullPath($path);
        $directory = dirname($fullPath);

        $this->makeDirectory($directory);
        $this->makeFile($fullPath, $path, $model);
    }

    /**
     * Generate the full path for the repository class file.
     *
     * @param string $path The relative path to the repository class file
     * @return string The full path to the repository class file
     */
    public function generateFullPath($path)
    {
        // Get the base directory for repositories
        // $baseDirectory = app_path('Repositories');
        $baseDirectory = $this->laravel->path('Repositories');
        // Generate the full path by replacing backslashes with forward slashes
        $fullPath = $baseDirectory . '/' . str_replace('\\', '/', $path) . '.php';
        return $fullPath;
    }

    /**
     * Create the directory if it doesn't exist.
     *
     * @param string $directory The directory path
     */
    public function makeDirectory($directory)
    {
        // Check if the directory already exists
        if (!File::isDirectory($directory)) {
            // Create the directory with read, write, execute permissions for owner and group
            File::makeDirectory($directory, 0777, true);
            $this->info("Directory created at: $directory");
        } else {
            $this->info("Directory already exists at: $directory");
        }
    }

    /**
     * Create the file with the repository class.
     *
     * @param string $fullPath The full path to the repository class file
     * @param string $path The relative path to the repository class file
     */
    // public function makeFile($fullPath, $path)
    // {
    //     // Check if the file already exists
    //     if (!File::exists($fullPath)) {
    //         // Get the class name and namespace path
    //         $className = basename($path);
    //         $namespacePath = dirname($path);
    //         $namespace = 'App\\Repositories';

    //         // Add the namespace path to the namespace
    //         if ($namespacePath !== '.') {
    //             $namespace .= '\\' . str_replace('/', '\\', $namespacePath);
    //         }

    //         // Create the file with the repository class
    //         File::put($fullPath, "<?php\n\nnamespace $namespace;\n\nclass $className\n{\n    // Repository methods\n}\n");
    //         $this->info("Repository created at: $fullPath");
    //     } else {
    //         $this->error("Repository already exists at: $fullPath");
    //     }
    // }


    public function makeFile($fullPath, $path, $model)
    {
        if (File::exists($fullPath)) {
            return $this->error("Repository already exists at: $fullPath");
        }

        $className = basename($path);
        $namespacePath = dirname($path);

        $namespace = 'App\\Repositories';
        if ($namespacePath !== '.') {
            $namespace .= '\\' . str_replace('/', '\\', $namespacePath);
        }

        if ($model) {
            $methods = $this->generateCrud($model);
            $modelUse = "use App\\Models\\$model;";
        } else {
            $methods = "    // Repository methods\n";
            $modelUse = ""; // مهم: ما نحطش use فارغ
        }

        $template = "<?php

namespace $namespace;

$modelUse

class $className
{
$methods
}
";

        File::put($fullPath, $template);
        $this->info("Repository created at: $fullPath");
    }


    private function generateCrud($model)
    {
        return <<<CRUD
    /**
     * Get all with condition + with relations + optional pagination + orderBy
     */
    public function allWithConidtion(array \$condition, array \$with = [], \$paginated = false, \$limit = 10, array \$orderBy = [])
    {
        \$query = $model::with(\$with)->where(\$condition);

        if (!empty(\$orderBy)) {
            foreach (\$orderBy as \$column => \$direction) {
                \$query->orderBy(\$column, \$direction);
            }
        }

        return \$paginated ? \$query->paginate(\$limit) : \$query->get();
    }

    /**
     * Find by ID with optional condition & relations
     */
    public function find(\$id, array \$with = [], array \$condition = [])
    {
        return $model::with(\$with)->where(\$condition)->whereId(\$id)->first();
    }

    /**
     * Create new record
     */
    public function create(array \$data)
    {
        return $model::create(\$data);
    }

    /**
     * Update record
     */
    public function update(\$id, array \$data)
    {
        \$record = $model::findOrFail(\$id);
        \$record->update(\$data);
        return \$record;
    }

    /**
     * Delete record with optional condition
     */
    public function delete(\$id, array \$condition = [])
    {
        return $model::where(\$condition)->whereId(\$id)->delete();
    }
CRUD;
    }
}
