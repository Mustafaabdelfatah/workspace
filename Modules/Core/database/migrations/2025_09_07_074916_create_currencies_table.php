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
        Schema::connection($this->coreConnection)->create('currencies', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('symbol')->nullable();
            $table->json('short_form')->nullable();
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
        Schema::connection($this->coreConnection)->dropIfExists('currencies');
    }
};
