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
        $this->coreDatabase = config("database.connections.{$this->coreConnection}.database");
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection($this->coreConnection)->create('time_entries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('task_id')
                  ->constrained('tasks')
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained("{$this->coreDatabase}.users")
                  ->cascadeOnDelete();

            $table->text('description')->nullable();
            $table->datetime('start_time');
            $table->datetime('end_time')->nullable();
            $table->decimal('hours', 8, 2);

            $table->boolean('is_billable')->default(false);
            $table->decimal('hourly_rate', 10, 2)->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['task_id', 'user_id']);
            $table->index(['start_time']);
            $table->index(['is_billable']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->coreConnection)->dropIfExists('time_entries');
    }
};
