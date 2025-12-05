<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('group_key')->nullable();
            $table->string('name');
            $table->foreignId('module_id')->nullable()->constrained('modules')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('group_has_permissions', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->primary(['permission_id', 'group_id']);
        });

        Schema::create('model_has_groups', function (Blueprint $table) {
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->morphs('model');
            $table->unsignedBigInteger('writer_id')->nullable();
            $table->primary(['group_id', 'model_id', 'model_type']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('model_has_groups');
        Schema::dropIfExists('group_has_permissions');
        Schema::dropIfExists('groups');
    }
};
