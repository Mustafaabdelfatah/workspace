<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
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
        Schema::connection($this->coreConnection)->create('model_otps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('otp_id')->constrained('otps')->cascadeOnDelete();
            $table->nullableMorphs('otpable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->coreConnection)->dropIfExists('model_otps');
    }
};
