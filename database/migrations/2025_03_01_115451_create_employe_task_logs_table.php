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
        Schema::create('employe_task_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employe_task_id');
            $table->longText('log');
            $table->timestamps();
            $table->foreign('employe_task_id')->references('id')->on('employee_tasks')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employe_task_logs');
    }
};
