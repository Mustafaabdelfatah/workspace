<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\ProjectManagement\App\Enums\ProjectMemberRoleEnum;

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
        Schema::connection($this->coreConnection)->create('project_members', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')
                  ->constrained('projects')
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained("{$this->coreDatabase}.users")
                  ->cascadeOnDelete();

            $table->string('role')->default(ProjectMemberRoleEnum::MEMBER->value);
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();

            $table->unique(['project_id', 'user_id']);
            $table->index(['project_id', 'role']);
            $table->index(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::connection($this->coreConnection)->dropIfExists('project_members');
    }
};
