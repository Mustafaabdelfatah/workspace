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
        Schema::connection($this->coreConnection)->create('invitation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained('invitations')->cascadeOnDelete(); 
            $table->string('scope_type');
            $table->unsignedBigInteger('scope_id')->nullable();
            $table->foreignId('group_id')->nullable()->constrained("groups")->nullOnDelete();
            $table->timestamps();
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->coreConnection)->dropIfExists('invitation_items');
    }
};

