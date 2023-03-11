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
        Schema::create('shop_products_variations_values', function (Blueprint $table) {
            $table->foreignId('product_variation_id')->constrained('shop_products_variations')->cascadeOnDelete();
            $table->foreignId('variation_value_id')->constrained('shop_variations_values')->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['product_variation_id', 'variation_value_id'], 'pk_shop_products_variations_values');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_products_variations_values');
    }
};
