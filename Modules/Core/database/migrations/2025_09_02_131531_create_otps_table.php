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
        Schema::connection($this->coreConnection)->create('otps', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->enum('bearer', config('core.otp_bearers'));
            $table->enum('action_type', config('core.otp_action_types'));
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('status')->default(0);
            $table->dateTime('expired_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->coreConnection)->dropIfExists('otps');
    }
};
