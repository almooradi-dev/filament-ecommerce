<?php

use Almooradi\FilamentEcommerce\Constants\ProductStatus;
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
		Schema::table('shop_products', function (Blueprint $table) {
			$table->string('title')->nullable()->change();
			$table->string('slug')->nullable()->change();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('shop_products', function (Blueprint $table) {
			$table->string('title')->nullable(false)->change();
			$table->string('slug')->nullable(false)->change();
		});
	}
};
