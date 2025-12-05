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
    private string $coreDatabase;
    public function __construct()
    {
        $this->coreConnection = config('core.database_connection');
        $this->coreDatabase = config('database.connections.' . $this->coreConnection . '.database');
    }
    public function up()
    {
        Schema::connection($this->coreConnection)->create('cities', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->string('nationality');
            $table->foreignId('writer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('editor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
         });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->coreConnection)->dropIfExists('cities');
    }
};
