<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Database\Seeders\CoreDatabaseSeeder;
use Modules\ProjectManagement\Database\Seeders\ProjectManagementDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         $this->call(CoreDatabaseSeeder::class);
         $this->call(ProjectManagementDatabaseSeeder::class);
        //  $this->call(LawDatabaseSeeder::class);
    }
}
