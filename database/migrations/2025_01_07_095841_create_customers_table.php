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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('zone_id')->nullable();
            $table->string('odp_id')->nullable();
            $table->string('name');
            $table->string('phone');
            $table->longText('address');
            $table->string('latitude');
            $table->string('longitude');
            $table->string('sn_modem')->nullable();
            $table->timestamps();
            $table->foreign('branch_id')->references('id')->on('branches')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('zone_id')->references('id')->on('zone_odps')->cascadeOnDelete()->cascadeOnUpdate();
            // $table->foreign('odp_id')->references('id')->on('odps')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
