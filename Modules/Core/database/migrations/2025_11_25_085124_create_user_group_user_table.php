<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $coreConnection;
    private string $coreDatabase;

    public function __construct()
    {
        $this->coreConnection = config('core.database_connection');
        $this->coreDatabase = config('database.connections.' . $this->coreConnection . '.database');
    }


    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection($this->coreConnection)->create('user_group_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained("users")->cascadeOnDelete(); 
            $table->foreignId('user_group_id')->constrained("user_groups")->cascadeOnDelete(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->coreConnection)->dropIfExists('user_group_user');
    }
};

