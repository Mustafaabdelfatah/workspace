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

    public function up(): void
    {
        Schema::connection($this->coreConnection)->create('tasks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')
                  ->constrained('projects')
                  ->cascadeOnDelete();

            $table->foreignId('parent_task_id')
                  ->nullable()
                  ->constrained('tasks')
                  ->nullOnDelete();

            $table->json('title');
            $table->string('code')->unique();
            $table->json('description')->nullable();

            $table->string('status')->default('pending');
            $table->string('priority')->default('medium');

            $table->foreignId('assigned_to')
                  ->nullable()
                  ->constrained("{$this->coreDatabase}.users")
                  ->nullOnDelete();

            $table->foreignId('created_by')
                  ->constrained("{$this->coreDatabase}.users")
                  ->cascadeOnDelete();

            $table->datetime('start_date')->nullable();
            $table->datetime('due_date')->nullable();
            $table->datetime('completed_at')->nullable();

            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->decimal('actual_hours', 8, 2)->nullable();
            $table->integer('progress_percentage')->default(0);

            $table->json('tags')->nullable();
            $table->json('settings')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['project_id', 'status']);
            $table->index(['assigned_to']);
            $table->index(['created_by']);
            $table->index(['parent_task_id']);
            $table->index(['code']);
            $table->index(['due_date']);
        });
    }

    public function down(): void
    {
        Schema::connection($this->coreConnection)->dropIfExists('tasks');
    }
};
