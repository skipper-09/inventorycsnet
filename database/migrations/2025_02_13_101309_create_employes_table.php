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
        Schema::create('employes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('departement_id');
            $table->unsignedBigInteger('position_id');
            $table->string('name');
            $table->text('address');
            $table->string('phone');
            $table->string('email')->unique();
            $table->date('date_of_birth');
            $table->enum('gender',['male','female'])->default('male');
            $table->string('nik')->unique();
            $table->longText('identity_card');
            $table->timestamps();
            $table->foreign('departement_id')->references('id')->on('departements')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('position_id')->references('id')->on('positions')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employes');
    }
};
