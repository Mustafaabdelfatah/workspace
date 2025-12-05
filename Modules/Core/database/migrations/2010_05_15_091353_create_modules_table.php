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
    public function up(): void
    {
        Schema::connection($this->coreConnection)->create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('module_key')->unique();;
            $table->tinyInteger('is_enabled')->default(1);
            $table->string('frontend_slug')->unique();
            $table->string('module_name');
            $table->foreignId('editor_id')->nullable()->constrained("users")->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->coreConnection)->dropIfExists('modules');
    }
};
