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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('work_schedule_id')->nullable();
            $table->time('clock_in');
            $table->time('clock_out')->nullable();
            $table->enum('clock_in_status',['normal','late'])->default('normal');
            $table->enum('clock_out_status',['normal','early'])->default('normal')->nullable();
            $table->longText('clock_in_image');
            $table->longText('clock_out_image');
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('work_schedule_id')->references('id')->on('work_schedules')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
