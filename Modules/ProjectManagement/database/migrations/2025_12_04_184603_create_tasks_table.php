<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\ProjectManagement\App\Enums\TaskStatus;
use Modules\ProjectManagement\App\Enums\TaskPriority;
use Modules\ProjectManagement\App\Enums\TaskPriorityEnum;
use Modules\ProjectManagement\App\Enums\TaskStatusEnum;
use Modules\ProjectManagement\App\Enums\TaskType;
use Modules\ProjectManagement\App\Enums\TaskTypeEnum;

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
                  ->constrained("{$this->coreDatabase}.projects")
                  ->cascadeOnDelete();
            $table->foreignId('assignee_id')
                  ->nullable()
                  ->constrained("{$this->coreDatabase}.users")
                  ->nullOnDelete();
            $table->foreignId('reporter_id')
                  ->nullable()
                  ->constrained("{$this->coreDatabase}.users")
                  ->nullOnDelete();
            $table->foreignId('parent_task_id')
                  ->nullable()
                  ->constrained("{$this->coreDatabase}.tasks")
                  ->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('task_code')->nullable()->unique();
            $table->string('type')->default(TaskTypeEnum::TASK->value);
            $table->string('priority')->default(TaskPriorityEnum::MEDIUM->value);
            $table->string('status')->default(TaskStatusEnum::TODO->value);
            $table->integer('story_points')->nullable();
            $table->integer('estimated_hours')->nullable();
            $table->integer('actual_hours')->nullable()->default(0);
            $table->date('due_date')->nullable();
            $table->date('start_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('position')->default(0);
            $table->softDeletes();
            $table->timestamps();
            $table->index(['project_id', 'status']);
            $table->index(['assignee_id', 'status']);
            $table->index(['project_id', 'assignee_id']);
            $table->index(['due_date']);
            $table->index(['task_code']);
        });
    }

    public function down(): void
    {
        Schema::connection($this->coreConnection)->dropIfExists('tasks');
    }
};