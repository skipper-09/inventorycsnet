<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_detail_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('task_assign_id');
            $table->enum('status',['complated','pending','overdue', 'in_review'])->default('pending');
            $table->foreign('task_detail_id')->references('id')->on('task_details')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('task_assign_id')->references('id')->on('task_assigns')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_tasks');
    }
};
