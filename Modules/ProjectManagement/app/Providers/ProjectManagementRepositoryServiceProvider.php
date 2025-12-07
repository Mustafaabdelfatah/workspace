<?php

namespace Modules\ProjectManagement\App\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\ProjectManagement\App\Repositories\ProjectRepositoryInterface;
use Modules\ProjectManagement\App\Repositories\ProjectRepository;

class ProjectManagementRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        // Bind repository interfaces to concrete implementations
        $this->app->bind(ProjectRepositoryInterface::class, ProjectRepository::class);
    }

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        //
    }
}
