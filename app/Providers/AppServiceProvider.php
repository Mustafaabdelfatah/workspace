<?php

namespace App\Providers;

use App\Channels\FCMChannel;
use App\Channels\CustomDbChannel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $modulesFile = base_path('modules_statuses.json');
        $modules = json_decode(file_get_contents($modulesFile), true);
        $graphQLSchemasDefaults = [];
        $graphQLSchemasAuth = [];

        foreach ($modules ?? [] as $moduleKey => $status) {
            $path = base_path("Modules/$moduleKey/config/graphql.php");
            if ($status && file_exists($path)) {
                $moduleGraph = include $path;
                $graphQLSchemasDefaults['query'] = array_merge($graphQLSchemasDefaults['query'] ?? [], $moduleGraph['not_auth']['query'] ?? []);
                $graphQLSchemasDefaults['mutation'] = array_merge($graphQLSchemasDefaults['mutation'] ?? [], $moduleGraph['not_auth']['mutation'] ?? []);
                $graphQLSchemasDefaults['types'] = array_merge($graphQLSchemasDefaults['types'] ?? [], $moduleGraph['not_auth']['type'] ?? []);

                $graphQLSchemasAuth['query'] = array_merge($graphQLSchemasAuth['query'] ?? [], $moduleGraph['auth']['query'] ?? []);
                $graphQLSchemasAuth['mutation'] = array_merge($graphQLSchemasAuth['mutation'] ?? [], $moduleGraph['auth']['mutation'] ?? []);
                $graphQLSchemasAuth['types'] = array_merge($graphQLSchemasAuth['types'] ?? [], $moduleGraph['auth']['type'] ?? []);
            }
        }

        config([
            'graphql.schemas.default.query' => $graphQLSchemasDefaults['query'],
            'graphql.schemas.default.mutation' => $graphQLSchemasDefaults['mutation'],
            'graphql.schemas.default.types' => $graphQLSchemasDefaults['types'],
            'graphql.schemas.auth.query' => $graphQLSchemasAuth['query'],
            'graphql.schemas.auth.mutation' => $graphQLSchemasAuth['mutation'],
            'graphql.schemas.auth.types' => $graphQLSchemasAuth['types'],
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Storage::macro('toBase64', function ($path) {
            $path = decrypt($path);

            if (!Storage::disk('local')->exists($path)) {
                return null;
            }
            $fileContent = Storage::disk('local')->get($path);
            $fileBase64 = base64_encode($fileContent);
            $mimeType = Storage::disk('local')->mimeType($path);

            return "data:$mimeType;base64,$fileBase64";
        });
        Storage::macro('toBase64WithoutEncrypted', function ($path) {
            if (!Storage::disk('local')->exists($path)) {
                return null;
            }
            $fileContent = Storage::disk('local')->get($path);
            $fileBase64 = base64_encode($fileContent);
            $mimeType = Storage::disk('local')->mimeType($path);

            return "data:$mimeType;base64,$fileBase64";
        });

        Blueprint::macro('dropForeignIfExists', function ($index) {
            $table = $this->getTable();
            foreach ((array) $index as $indexItem) {
                $indexName = is_array($indexItem)
                    ? implode('_', $indexItem)
                    : $indexItem;
                $defaultKey = "{$table}_{$indexName}_foreign";

                $exists = DB::selectOne('
                    SELECT CONSTRAINT_NAME
                    FROM information_schema.KEY_COLUMN_USAGE
                    WHERE TABLE_SCHEMA = DATABASE()
                        AND TABLE_NAME = ?
                        AND (CONSTRAINT_NAME = ? OR CONSTRAINT_NAME = ?)
                    LIMIT 1
                ', [$table, $indexItem, $defaultKey]);

                if ($exists) {
                    $this->dropForeign(is_array($indexItem) ? $indexItem : [$indexItem]);
                }
            }
        });

        Schema::macro('hasDatabase', function ($databaseName) {
            $result = DB::select(
                'SELECT SCHEMA_NAME 
                 FROM INFORMATION_SCHEMA.SCHEMATA 
                 WHERE SCHEMA_NAME = ?',
                [$databaseName]
            );

            return count($result) > 0;
        });

        app(ChannelManager::class)->extend('fcm', function ($app) {
            return new FCMChannel();
        });

      
    }
}
