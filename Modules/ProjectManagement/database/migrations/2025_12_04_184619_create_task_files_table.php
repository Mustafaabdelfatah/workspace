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
        Schema::connection($this->coreConnection)->create('task_files', function (Blueprint $table) {
            $table->id();

            $table->foreignId('task_id')
                  ->constrained('tasks')
                  ->cascadeOnDelete();

            $table->string('original_name');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->bigInteger('file_size');
            $table->string('file_type')->default('attachment'); // attachment, image, document

            $table->foreignId('uploaded_by')
                  ->constrained("{$this->coreDatabase}.users")
                  ->cascadeOnDelete();

            $table->json('metadata')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();

            $table->index(['task_id', 'file_type']);
            $table->index(['uploaded_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->coreConnection)->dropIfExists('task_files');
    }
};
