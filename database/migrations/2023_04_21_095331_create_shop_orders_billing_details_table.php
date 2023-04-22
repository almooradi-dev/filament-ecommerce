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
		Schema::create('shop_orders_billing_details', function (Blueprint $table) {
			$table->id();
			$table->foreignId('order_id')->constrained('shop_orders')->cascadeOnDelete(); // TODO: change "shop_" table from config
			$table->string('first_name');
			$table->string('father_name')->nullable();
			$table->string('last_name');
			$table->string('email')->nullable();
			$table->string('country_code')->nullable();
			$table->string('phone')->nullable();
			$table->string('country')->nullable();
			$table->string('city')->nullable();
			$table->string('state')->nullable();
			$table->string('address_1')->nullable();
			$table->string('address_2')->nullable();
			$table->string('zip_code')->nullable();
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
		Schema::dropIfExists('shop_orders_billing_details');
	}
};
