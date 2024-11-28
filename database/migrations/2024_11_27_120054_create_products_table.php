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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('sub_category_id');
            $table->string('metal_color');
            $table->string('metal');
            $table->string('diamond_color');
            $table->string('diamond_quality');
            $table->string('image');
            $table->string('clarity');
            $table->string('size_name');
            $table->unsignedBigInteger('size_id');
            $table->decimal('weight', 8, 2);
            $table->integer('no_of_diamonds');
            $table->string('diamond_setting');
            $table->string('diamond_shape');
            $table->string('collection');
            $table->string('gender');
            $table->text('description');
            $table->integer('qty');
            $table->decimal('price', 10, 2);
            $table->decimal('discount', 5, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('sub_category_id')->references('id')->on('sub_categories')->onDelete('cascade');
            $table->foreign('size_id')->references('id')->on('sizes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
