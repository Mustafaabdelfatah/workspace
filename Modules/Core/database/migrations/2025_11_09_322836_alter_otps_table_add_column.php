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

        Schema::connection($this->coreConnection)->table('otps', function (Blueprint $table) {
            $table->string('email_mobile')->nullable()->after('action_type');
            $table->unsignedBigInteger('user_id')->nullable(true)->change();
        });      
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->coreConnection)->table('otps', function (Blueprint $table) {
            $table->dropColumn('email_mobile');
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};
