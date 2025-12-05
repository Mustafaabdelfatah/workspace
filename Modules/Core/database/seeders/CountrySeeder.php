<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Models\Country;

class CountrySeeder extends Seeder
{
    private string $coreConnection;

    public function __construct()
    {
        $this->coreConnection = config('core.database_connection');
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configCountries = config('countries');
        Schema::connection($this->coreConnection)->disableForeignKeyConstraints();
        Country::truncate();

        foreach ($configCountries as $country) {
            Country::create([
                'name' => ['en' => $country['name_en'], 'ar' => $country['name_ar']],
                'code' => $country['code'],
            ]);
        }
        Schema::connection($this->coreConnection)->enableForeignKeyConstraints();
    }
}
