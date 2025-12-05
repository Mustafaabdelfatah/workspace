<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $coreConnection;

    public function __construct(){
        $this->coreConnection = config("core.database_connection"); 
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection($this->coreConnection)->create("tables_logs", function (Blueprint $table) {
            $table->id();
            $table->morphs("loggable");
            $table->foreignId("writer_id")->nullable()->constrained("users")->nullOnDelete();
            $table->foreignId("editor_id")->nullable()->constrained("users")->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->coreConnection)->dropIfExists("tables_logs");
    }
};
