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

        Schema::connection($this->coreConnection)->table('users', function (Blueprint $table) {
            $table->foreignId('default_workspace_id')->nullable()->after('is_admin')->constrained('workspaces')->nullOnDelete();

        });      
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->coreConnection)->table('users', function (Blueprint $table) {
            $table->dropForeignIfExists(['default_workspace_id']);
            $table->dropColumn('default_workspace_id');

        });
    }
};
