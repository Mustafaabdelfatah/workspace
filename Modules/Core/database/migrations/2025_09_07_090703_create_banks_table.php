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
        Schema::connection($this->coreConnection)->create('banks', function (Blueprint $table){
            $table->id();
            $table->string('bank_name');
            $table->string('bank_short_code');
            $table->foreignId('writer_id')->nullable()->constrained("users")->nullOnDelete();
            $table->foreignId('editor_id')->nullable()->constrained("users")->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->coreConnection)->dropIfExists('banks');
    }
};
