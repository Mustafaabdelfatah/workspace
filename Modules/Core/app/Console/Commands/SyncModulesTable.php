<?php

namespace Modules\Core\Console\Commands;

use Nwidart\Modules\Facades\Module;
use Illuminate\Console\Command;


class SyncModulesTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'core:sync-modules-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $modules = Module::all();

        foreach ($modules as $name => $module) {
            $moduleKey = $module->getName();
            $moduleName = [
                'en' => $module->getName(),
                'ar' => $module->getName(),
            ];
            $slug = strtolower($module->getName()); // or use custom logic

            \Modules\Core\Models\Module::updateOrInsert(
                ['module_key' => $moduleKey],
                [
                    'module_name' => \Safe\json_encode($moduleName),
                    'frontend_slug' => $slug,
                    'is_enabled' => 1,
                    'editor_id' => null,
                    'updated_at' => now(),
                    'created_at' => now(), // only used if it's inserting
                ]
            );

            $this->info("✅ Synced: {$module->getName()}");
        }

        $this->info('🎯 Modules table synced successfully!');

    }
}
