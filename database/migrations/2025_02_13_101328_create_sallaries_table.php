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
        Schema::create('sallaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employe_id');
            $table->date('sallary_month');
            $table->decimal('basic_sallary_amount',10,2);
            $table->decimal('bonus',10,2);
            $table->decimal('deduction',10,2);
            $table->decimal('allowance',10,2);
            $table->decimal('total_sallary',10,2);
            $table->boolean('payment_status')->default(true);
            $table->timestamps();
            $table->foreign('employe_id')->references('id')->on('employes')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sallaries');
    }
};
