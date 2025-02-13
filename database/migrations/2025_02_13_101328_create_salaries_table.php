<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('salary_month');
            $table->decimal('basic_salary_amount', 10, 2);
            $table->decimal('bonus', 10, 2);
            $table->decimal('deduction', 10, 2);
            $table->decimal('allowance', 10, 2);
            $table->decimal('total_salary', 10, 2);
            $table->boolean('payment_status')->default(true);
            $table->timestamps();
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
