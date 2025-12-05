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
        Schema::connection($this->coreConnection)->create('invitations', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->foreignId('workspace_id')->constrained("workspaces")->cascadeOnDelete(); 
            $table->string('email')->nullable();
            $table->foreignId('invited_user_id')->nullable()->constrained("users")->nullOnDelete(); 
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->foreignId('writer_id')->nullable()->constrained("users")->nullOnDelete(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->coreConnection)->dropIfExists('invitations');
    }
};

