<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register any module-specific bindings here
    }

    public function boot(): void
    {
        $modulePath = app_path('Modules');
        
        if (!is_dir($modulePath)) {
            return;
        }

        $modules = scandir($modulePath);

        foreach ($modules as $module) {
            if ($module === '.' || $module === '..') {
                continue;
            }

            $moduleDir = $modulePath . '/' . $module;

            if (is_dir($moduleDir)) {
                // Load Routes
                $apiRoutes = $moduleDir . '/Routes/api.php';
                if (file_exists($apiRoutes)) {
                    Route::prefix('api/v1')
                        ->middleware('api')
                        ->namespace("App\\Modules\\{$module}\\Controllers\\V1")
                        ->group($apiRoutes);
                }

                // Load Migrations
                $migrationPath = $moduleDir . '/Database/Migrations';
                if (is_dir($migrationPath)) {
                    $this->loadMigrationsFrom($migrationPath);
                }
            }
        }
    }
}
