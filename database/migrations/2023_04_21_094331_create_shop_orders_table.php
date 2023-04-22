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
        Schema::create('shop_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // TODO: Can select "users" table from config
			$table->string('uid')->nullable();
			$table->string('payment_method')->nullable();
			$table->string('status');
			$table->string('status_notes')->nullable();
			$table->string('shipping_status')->nullable();
			$table->string('shipping_status_notes')->nullable();
			$table->decimal('shipping_cost')->nullable();
			$table->decimal('subtotal')->nullable();
			$table->decimal('total');
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
        Schema::dropIfExists('shop_orders');
    }
};
