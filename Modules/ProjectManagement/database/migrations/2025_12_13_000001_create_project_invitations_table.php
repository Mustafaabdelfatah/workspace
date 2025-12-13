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
        Schema::connection($this->coreConnection)->create('project_invitations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')
                  ->constrained('projects')
                  ->cascadeOnDelete();

            $table->string('token')->unique();

            // Individual user invitation
            $table->foreignId('invited_user_id')
                  ->nullable()
                  ->constrained("{$this->coreDatabase}.users")
                  ->nullOnDelete();

            // Group invitation
            $table->foreignId('user_group_id')
                  ->nullable()
                  ->constrained("{$this->coreDatabase}.user_groups")
                  ->nullOnDelete();

            $table->string('email')->nullable();
            $table->string('role')->default(ProjectMemberRoleEnum::MEMBER->value);
            $table->timestamp('invitation_date')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('declined_at')->nullable();

            $table->foreignId('invited_by')
                  ->constrained("{$this->coreDatabase}.users")
                  ->cascadeOnDelete();

            $table->text('message')->nullable();
            $table->json('settings')->nullable();

            $table->timestamps();

            $table->index(['project_id', 'invited_user_id']);
            $table->index(['project_id', 'user_group_id']);
            $table->index(['token']);
            $table->index(['email']);
        });
    }

    public function down(): void
    {
        Schema::connection($this->coreConnection)->dropIfExists('project_invitations');
    }
};
