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
        Schema::connection($this->coreConnection)->create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('national_id')->nullable();
            $table->string('license_number')->nullable();
            $table->string('mobile');
            $table->string('other_mobile')->nullable();
            $table->string('name');
            $table->boolean('status')->default(1);
            $table->string('password')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('license_path')->nullable();
            $table->string('signature_path')->nullable();
            $table->timestamp('signature_verified_at')->nullable();
            $table->timestamp('terms_and_conditions_verified_at')->nullable();
            $table->boolean('is_admin')->default(0);
            $table->boolean('is_national_id_verified')->default(0);
            $table->string('theme_color')->nullable();
            $table->dateTime('last_notifications_click')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->coreConnection)->dropIfExists('users');
    }
};
