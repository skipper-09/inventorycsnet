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
        Schema::create('task_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_task_id');
            $table->enum('report_type',['before','after'])->default('before');
            $table->longText('report_image');
            $table->longText('report_content')->nullable();
            $table->timestamps();
            $table->foreign('employee_task_id')->references('id')->on('employee_tasks')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_reports');
    }
};
