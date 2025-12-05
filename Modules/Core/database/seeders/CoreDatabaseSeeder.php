<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\User;
use Modules\Core\Models\Permission;
use Nwidart\Modules\Facades\Module;

class CoreDatabaseSeeder extends Seeder
{
    protected $coreConnection = 'core';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            CountrySeeder::class
        ]);


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
                    'created_at' => now()
                ]
            );
        }


        User::firstOrCreate([
            'id' => 1,
        ], [
            'username' => 'admin',
            'mobile' => '966555555555',
            'name' => ['en' => 'Admin', 'ar' => 'Admin'],
            'email' => 'admin@admin.com',
            'password' => 12345678,
            'is_admin' => 1,
            'status' => 1
        ]);



        $permissions = [
            ['name' => 'view', 'guard_name' => 'api', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'add', 'guard_name' => 'api', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'edit', 'guard_name' => 'api', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'delete', 'guard_name' => 'api', 'created_at' => now(), 'updated_at' => now()],
        ];
        
        Permission::upsert($permissions, ['name', 'guard_name']);

        
        
    }
}
