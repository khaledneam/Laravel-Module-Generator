<?php

namespace Khaledneam\ModuleGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeModuleCommand extends Command
{
    protected $signature = 'make:module {name}';
    protected $description = 'Create a new module';

    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $moduleName = $this->argument('name');
        $modulePath = base_path("Modules/{$moduleName}");

        // Ensure the module path is valid
        if ($this->files->exists($modulePath)) {
            $this->error("Module '{$moduleName}' already exists.");
            return;
        }

        $this->createModuleDirectories($modulePath);
        $this->createServiceProvider($moduleName, $modulePath);
        $this->createController($moduleName, $modulePath);
        $this->createView($moduleName, $modulePath);
        $this->createRoutes($moduleName, $modulePath);
        $this->createRepository($moduleName, $modulePath);
        $this->createService($moduleName, $modulePath);
        $this->registerServiceProvider($moduleName);
        $this->registerModuleRoutes($moduleName);
        $this->updateHelpersFile();
    }

    protected function createModuleDirectories($modulePath)
    {
        $directories = ['Controllers', 'Models', 'Requests', 'Routes', 'Views', 'Migrations', 'Tests', 'Providers', 'Repositories', 'Services'];

        foreach ($directories as $directory) {
            $dirPath = "{$modulePath}/{$directory}";
            if (!$this->files->exists($dirPath)) {
                $this->files->makeDirectory($dirPath, 0755, true);
                $this->info("Created folder: {$directory}");
            }
        }
    }

    protected function createServiceProvider($moduleName, $modulePath)
    {
        $providerPath = "{$modulePath}/Providers/{$moduleName}ServiceProvider.php";
        $providerContent = "<?php

namespace Modules\\{$moduleName}\\Providers;

use Illuminate\Support\ServiceProvider;

class {$moduleName}ServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register any application services.
    }

    public function boot()
    {
        // Register module views
        \$this->loadViewsFrom(module_path('{$moduleName}', 'Views'), '{$moduleName}');

        // Register module routes
        \$this->loadRoutesFrom(module_path('{$moduleName}', 'Routes/web.php'));
    }
}
";
        $this->files->put($providerPath, $providerContent);
        $this->info("Created service provider: Providers/{$moduleName}ServiceProvider.php");
    }

    protected function createController($moduleName, $modulePath)
    {
        $controllerPath = "{$modulePath}/Controllers/{$moduleName}Controller.php";
        $controllerContent = "<?php

namespace Modules\\{$moduleName}\\Controllers;

use App\Http\Controllers\Controller;

class {$moduleName}Controller extends Controller
{
    public function index()
    {
        return view('{$moduleName}::index');
    }
}
";
        $this->files->put($controllerPath, $controllerContent);
        $this->info("Created controller: Controllers/{$moduleName}Controller.php");
    }

    protected function createView($moduleName, $modulePath)
    {
        $viewPath = "{$modulePath}/Views/index.blade.php";
        $viewContent = "<!-- This is the index view for the {$moduleName} module -->\n<h1>Welcome to the {$moduleName} module</h1>\n";
        $this->files->put($viewPath, $viewContent);
        $this->info("Created view file: Views/index.blade.php");
    }

    protected function createRoutes($moduleName, $modulePath)
    {
        $routesPath = "{$modulePath}/Routes/web.php";
        $routesContent = "<?php

use Illuminate\Support\Facades\Route;
use Modules\\{$moduleName}\\Controllers\\{$moduleName}Controller;

Route::get('/{$moduleName}', [{$moduleName}Controller::class, 'index']);
";
        $this->files->put($routesPath, $routesContent);
        $this->info("Created route file: Routes/web.php");
    }

    protected function createRepository($moduleName, $modulePath)
    {
        $repositoryPath = "{$modulePath}/Repositories/{$moduleName}Repository.php";
        $repositoryContent = "<?php

namespace Modules\\{$moduleName}\\Repositories;

class {$moduleName}Repository
{
    // Define repository methods here
}
";
        $this->files->put($repositoryPath, $repositoryContent);
        $this->info("Created repository: Repositories/{$moduleName}Repository.php");
    }

    protected function createService($moduleName, $modulePath)
    {
        $servicePath = "{$modulePath}/Services/{$moduleName}Service.php";
        $serviceContent = "<?php

namespace Modules\\{$moduleName}\\Services;

class {$moduleName}Service
{
    // Define service methods here
}
";
        $this->files->put($servicePath, $serviceContent);
        $this->info("Created service: Services/{$moduleName}Service.php");
    }

    protected function registerServiceProvider($moduleName)
    {
        $appConfigPath = base_path('config/app.php');
        $appConfigContent = $this->files->get($appConfigPath);

        $providerClass = "Modules\\{$moduleName}\\Providers\\{$moduleName}ServiceProvider::class";

        // Add the provider only if it's not already registered
        if (!str_contains($appConfigContent, $providerClass)) {
            $appConfigContent = str_replace(
                "'providers' => [",
                "'providers' => [\n        {$providerClass},",
                $appConfigContent
            );

            $this->files->put($appConfigPath, $appConfigContent);
            $this->info("Registered service provider in config/app.php");
        }
    }

    protected function registerModuleRoutes($moduleName)
    {
        $webRoutesPath = base_path('routes/web.php');
        $requireStatement = "\n// Routes for {$moduleName} module\nrequire base_path('Modules/{$moduleName}/Routes/web.php');\n";

        $existingContent = $this->files->get($webRoutesPath);

        // Add the require statement only if it's not already included
        if (!str_contains($existingContent, "Modules/{$moduleName}/Routes/web.php")) {
            $this->files->append($webRoutesPath, $requireStatement);
            $this->info("Registered routes for module: {$moduleName} in routes/web.php");
        }
    }

    protected function updateHelpersFile()
    {
        $helpersPath = base_path('app/helpers.php');
        $helpersContent = "<?php\n\nif (!function_exists('module_path')) {\n    function module_path(\$module, \$path = '')\n    {\n        return base_path('Modules/' . \$module . '/' . \$path);\n    }\n}\n";

        if (!$this->files->exists($helpersPath)) {
            $this->files->put($helpersPath, $helpersContent);
            $this->info('Created helpers.php file with module_path function.');
        } else {
            $existingContent = $this->files->get($helpersPath);

            if (!str_contains($existingContent, 'module_path')) {
                $updatedContent = $existingContent . "\n\n" . $helpersContent;
                $this->files->put($helpersPath, $updatedContent);
                $this->info('Updated helpers.php file with module_path function.');
            }
        }
    }
}
