<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\ProjectManagement\App\Enums\ProjectStatus;
use Modules\ProjectManagement\App\Enums\ProjectStatusEnum;
use Modules\ProjectManagement\App\Enums\ProjectTypeEnum;

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
        Schema::connection($this->coreConnection)->create('projects', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('workspace_id')
                ->constrained("{$this->coreDatabase}.workspaces")
                ->cascadeOnDelete();
            $table
                ->foreignId('owner_id')
                ->constrained("{$this->coreDatabase}.users")
                ->cascadeOnDelete();
            $table
                ->foreignId('manager_id')
                ->nullable()
                ->constrained("{$this->coreDatabase}.users")
                ->nullOnDelete();

            $table->foreignId('parent_project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->json('name');
            $table->string('code')->unique();
            $table->json('description')->nullable();
            $table->string('status')->default(ProjectStatusEnum::PLANNING->value);
            $table->string('project_type')->default(ProjectTypeEnum::RESIDENTIAL->value);
            $table->string('building_type')->nullable();
            // $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            // $table->foreignId('company_position_id')->nullable()->constrained('company_positions')->nullOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('settings')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('area', 10, 2)->nullable();
            $table->string('area_unit')->default('m²')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['workspace_id', 'status']);
            $table->index(['owner_id']);
            $table->index(['code']);
        });
    }

    public function down(): void
    {
        Schema::connection($this->coreConnection)->dropIfExists('projects');
    }
};
