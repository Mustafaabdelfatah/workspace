<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    private string $coreConnection;
    public function __construct()
    {
        $this->coreConnection = config('core.database_connection');
    }
    public function up()
    {
        Schema::connection($this->coreConnection)->create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->coreConnection)->dropIfExists('password_resets');
    }
};
