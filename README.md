# Laravel Module Generator

A Laravel package to quickly generate modular components such as Controllers, Models, Views, Repositories, and Services. This package helps in creating a modular structure in Laravel applications, making the code more organized and manageable.

## Features

- Create directories for Controllers, Models, Requests, Routes, Views, Migrations, Tests, Providers, Repositories, and Services.
- Generate Service Providers, Controllers, Views, Routes, Repositories, and Services files with basic templates.
- Automatically register the Service Provider in `config/app.php`.
- Automatically include module routes in `routes/web.php`.
- Update `helpers.php` file to include `module_path` function.

## Installation

1. **Install the package via Composer:**

   ```bash
   composer require khaledneam/module-generator dev-main --dev
   ```
2. Update composer.json
```bash
{
    "autoload": {
        "psr-4": {
            "Modules\\": "Modules/"
        }
    },
    "files": [
        "app/helpers.php"
    ]
}


```
Then, update Composer’s autoload files:
```bash
composer dump-autoload

```




Usage
To create a new module, use the following Artisan command:
```bash
php artisan make:module ModuleName
```

Example
```bash
php artisan make:module Blog
```
This will create a new module named Blog with the following structure:
```php

Modules/
    Blog/
        Controllers/
        Models/
        Requests/
        Routes/
        Views/
        Migrations/
        Tests/
        Providers/
        Repositories/
        Services/
```

Generated Files
```markdown
Service Provider: Modules/ModuleName/Providers/ModuleNameServiceProvider.php
Controller: Modules/ModuleName/Controllers/ModuleNameController.php
View: Modules/ModuleName/Views/index.blade.php
Route File: Modules/ModuleName/Routes/web.php
Repository: Modules/ModuleName/Repositories/ModuleNameRepository.php
Service: Modules/ModuleName/Services/ModuleNameService.php
```
Important Notes
``````
1.Register the Service Provider:
``````

Ensure that you add the newly created module's service provider to the providers array in config/app.php:

```php
'providers' => [
    // Other Service Providers
    Modules\ModuleName\Providers\ModuleNameServiceProvider::class,
],
```
Contributing
````
Feel free to fork the repository and submit pull requests. Any contributions are welcome.
````

