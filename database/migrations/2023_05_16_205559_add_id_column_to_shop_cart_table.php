<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
		Schema::table('shop_cart', function (Blueprint $table) {
			DB::unprepared('ALTER TABLE `shop_cart` DROP CONSTRAINT `shop_cart_user_id_foreign`');
			DB::unprepared('ALTER TABLE `shop_cart` DROP CONSTRAINT `shop_cart_product_id_foreign`');
			DB::unprepared('ALTER TABLE `shop_cart` DROP PRIMARY KEY');

			$table->id()->first();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('shop_products')->cascadeOnDelete(); 

			$table->unique(['user_id', 'product_id', 'key']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('shop_cart', function (Blueprint $table) {
			DB::unprepared('ALTER TABLE `shop_cart` DROP `id`');

			DB::unprepared('ALTER TABLE `shop_cart` DROP CONSTRAINT `shop_cart_user_id_foreign`');
			DB::unprepared('ALTER TABLE `shop_cart` DROP CONSTRAINT `shop_cart_product_id_foreign`');
			$table->dropUnique(['user_id', 'product_id', 'key']);

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('shop_products')->cascadeOnDelete(); 

            $table->primary(['user_id', 'product_id', 'key']);	 
		});
	}
};
