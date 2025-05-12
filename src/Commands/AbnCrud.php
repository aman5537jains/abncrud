<?php

namespace Aman5537jains\AbnCmsCRUD\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class AbnCrud extends Command
{
    protected $signature = 'make:abncrud 
                            {table : Table name (snake_case)} 
                            {--modelPath= : Optional custom model path} 
                            {--controllerPath= : Optional custom controller path}';

    protected $description = 'Generate a model and controller for a given table, using a custom stub and paths';

    public function handle()
    {
        $table = $this->argument('table');
        $modelName = Str::studly(Str::singular($table));
        $controllerName = "{$modelName}Controller";

        // Determine model path
        $modelPathOption = $this->option('modelPath');
        $modelPath = $modelPathOption 
            ? base_path(trim($modelPathOption, '/')) . "/{$modelName}.php"
            : app_path("Models/{$modelName}.php");

        // Determine controller path
        $controllerPathOption = $this->option('controllerPath');
        $controllerPath = $controllerPathOption 
            ? base_path(trim($controllerPathOption, '/')) . "/{$controllerName}.php"
            : app_path("Http/Controllers/{$controllerName}.php");

        // Generate model if not exists
        if (!File::exists($modelPath)) {
            $this->call('make:model', [
                'name' => $this->getModelNamespace($modelPath, $modelName)
            ]);
        } else {
            $this->warn("⚠️ Model already exists: {$modelPath}");
        }

        // Load custom controller stub
        
        $stubPath = __DIR__ . '/../stubs/custom-controller.stub';
        if (!File::exists($stubPath)) {
            $this->error("❌ Stub not found: {$stubPath}");
            return 1;
        }

        // Build namespace from controller path
        $relativePath = str_replace(base_path() . '/', '', dirname($controllerPath));
        $namespace = collect(explode('/', $relativePath))
            ->map(function($part){ return  Str::studly($part); })
            ->implode('\\');

        // Replace placeholders
        $stub = File::get($stubPath);
        $stub = str_replace(
            ['{{namespace}}', '{{modelName}}', '{{tableName}}', '{{moduleSlug}}', '{{moduleTitle}}'],
            [
                $namespace,
                $modelName,
                $table,
                Str::kebab(Str::pluralStudly($modelName)),
                Str::title(Str::pluralStudly($modelName)),
            ],
            $stub
        );

        File::ensureDirectoryExists(dirname($controllerPath));
       
        if (!File::exists($controllerPath)) {
            File::put($controllerPath, $stub);
        } else {
            $this->warn("⚠️ Controller already exists: {$controllerPath}");
        }

        $this->info("✅ Model created at: {$modelPath}");
        $this->info("✅ Controller created at: {$controllerPath}");

        return 0;
    }

    protected function getModelNamespace($path, $className)
    {
        $relative = str_replace(base_path() . '/', '', $path);
        $namespace = collect(explode('/', dirname($relative)))
                        ->map(function($part) { return  Str::studly($part); } )
                        ->implode('\\');

        return trim($namespace . '\\' . $className, '\\');
    }
}