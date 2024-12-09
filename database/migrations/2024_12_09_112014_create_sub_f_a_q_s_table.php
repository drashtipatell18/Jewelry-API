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
        Schema::create('sub_f_a_q_s', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('faq_id');
            $table->string('question');
            $table->string('answer');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('faq_id')->references('id')->on('f_a_q_s')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_f_a_q_s');
    }
};
