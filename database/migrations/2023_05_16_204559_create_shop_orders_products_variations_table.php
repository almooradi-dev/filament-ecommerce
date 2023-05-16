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
		Schema::create('shop_orders_products_variations_values', function (Blueprint $table) {
			$table->id();
			$table->foreignId('order_product_id')->constrained('shop_orders_products')->cascadeOnDelete(); // TODO: change "shop_" table from config
			$table->unsignedBigInteger('variation_value_id'); // No foreign key needed
			$table->string('variation_value_string');
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('shop_orders_products_variations_values');
	}
};
