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
        Schema::connection($this->coreConnection)->create('access_grants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained("workspaces")->cascadeOnDelete(); 
            $table->foreignId('user_id')->constrained("users")->cascadeOnDelete(); 
            $table->string('scope_type');
            $table->unsignedBigInteger('scope_id')->nullable();
            $table->foreignId('group_id')->nullable()->constrained("groups")->nullOnDelete();
            $table->timestamps();
            $table->unique(['workspace_id','user_id','scope_type','scope_id'], 'uniq_grant');

        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->coreConnection)->dropIfExists('access_grants');
    }
};

