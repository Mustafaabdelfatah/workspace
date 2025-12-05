<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Database\Seeders\CoreDatabaseSeeder;
use Modules\Law\Database\Seeders\LawDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         $this->call(CoreDatabaseSeeder::class);
        //  $this->call(LawDatabaseSeeder::class);
    }
}
