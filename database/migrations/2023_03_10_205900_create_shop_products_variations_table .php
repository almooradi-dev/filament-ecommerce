<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_products_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('shop_products')->cascadeOnDelete();
            $table->foreignId('variation_id')->constrained('shop_variations')->cascadeOnDelete();
            $table->foreignId('variation_value_id')->nullable()->constrained('shop_variations_values')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['product_id', 'variation_id', 'variation_value_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_products_variations');
    }
};
