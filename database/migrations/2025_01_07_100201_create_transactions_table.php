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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('to_branch')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->enum('type',['in','out'])->default('out');
            $table->enum('purpose',['stock_in','psb','repair','transfer'])->default('stock_in');
            $table->timestamps();
            $table->foreign('branch_id')->references('id')->on('branches')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('to_branch')->references('id')->on('branches')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
