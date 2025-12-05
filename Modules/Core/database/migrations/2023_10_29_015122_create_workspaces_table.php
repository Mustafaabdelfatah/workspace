<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $coreConnection;

    public function __construct()
    {
        $this->coreConnection = config('core.database_connection');
    }


    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection($this->coreConnection)->create('workspaces', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->foreignId('module_id')->nullable()->constrained("modules")->cascadeOnDelete();
            $table->string('workspace_type')->nullable();
            $table->foreignId('owner_id')->constrained("users")->cascadeOnDelete(); 
            $table->string('logo_path')->nullable();
            $table->string('a4_official_path')->nullable();
            $table->string('a4_draft_path')->nullable();
            $table->string('a4_unaccept_path')->nullable();
            $table->string('stamp_path')->nullable();
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
        Schema::connection($this->coreConnection)->dropIfExists('workspaces');
    }
};

