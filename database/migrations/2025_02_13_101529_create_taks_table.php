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
        Schema::create('taks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('departement_id');
            $table->unsignedBigInteger('employe_id');
            $table->unsignedBigInteger('task_template_id');
            $table->enum('task_type',['daily','weekly','monthly']);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->foreign('departement_id')->references('id')->on('departements')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('employe_id')->references('id')->on('employes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('task_template_id')->references('id')->on('taks_templates')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taks');
    }
};
