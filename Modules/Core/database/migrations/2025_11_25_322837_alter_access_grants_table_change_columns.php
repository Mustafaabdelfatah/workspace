<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    private string $coreConnection;

    public function __construct()
    {
        $this->coreConnection = config('core.database_connection');
    }
    public function up()
    {

        Schema::connection($this->coreConnection)->table('access_grants', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(true)->change();

        });      
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->coreConnection)->table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();

        });
    }
};
