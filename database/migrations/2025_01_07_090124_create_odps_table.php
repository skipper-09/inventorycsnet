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
        Schema::create('odps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('zone_id');
            $table->string('odp_id');
            $table->string('name');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->timestamps();
            $table->foreign('zone_id')->references('id')->on('zone_odps')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('odps');
    }
};
