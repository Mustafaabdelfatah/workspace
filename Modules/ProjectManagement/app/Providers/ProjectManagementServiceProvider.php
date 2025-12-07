<?php
namespace Modules\ProjectManagement\App\Providers;

use Illuminate\Support\ServiceProvider;

class ProjectManagementServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'ProjectManagement';
    protected string $moduleNameLower = 'projectmanagement';

    public function boot(): void
    {
        $this->registerConfig();
        $this->registerGraphQL();

        // Register repository service provider
        $this->app->register(ProjectManagementRepositoryServiceProvider::class);
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'config/config.php'),
            $this->moduleNameLower
        );
    }

    protected function registerGraphQL(): void
    {
        $graphqlConfig = config($this->moduleNameLower . '.graphql', []);

        if (empty($graphqlConfig)) {
            return;
        }

        if (isset($graphqlConfig['auth']['query'])) {
            config([
                'graphql.schemas.default.query' => array_merge(
                    config('graphql.schemas.default.query', []),
                    $graphqlConfig['auth']['query']
                )
            ]);
        }

        if (isset($graphqlConfig['auth']['mutation'])) {
            config([
                'graphql.schemas.default.mutation' => array_merge(
                    config('graphql.schemas.default.mutation', []),
                    $graphqlConfig['auth']['mutation']
                )
            ]);
        }

        if (isset($graphqlConfig['auth']['type'])) {
            config([
                'graphql.types' => array_merge(
                    config('graphql.types', []),
                    $graphqlConfig['auth']['type']
                )
            ]);
        }
    }
}
