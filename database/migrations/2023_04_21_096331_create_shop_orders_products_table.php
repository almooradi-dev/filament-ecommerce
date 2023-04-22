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
		Schema::create('shop_orders_products', function (Blueprint $table) {
			$table->id();
			$table->foreignId('order_id')->constrained('shop_orders')->cascadeOnDelete(); // TODO: change "shop_" table from config
			$table->foreignId('product_id')->constrained('shop_products')->cascadeOnDelete(); // TODO: change "shop_" table from config
			$table->integer('quantity');
			$table->decimal('unit_price');
			$table->text('notes')->nullable();
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
		Schema::dropIfExists('shop_orders_products');
	}
};
