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
        Schema::create('shop_cart', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();  
            $table->foreignId('product_id')->constrained('shop_products')->cascadeOnDelete();            
            $table->integer('quantity')->default(1);
            $table->string('key');
            $table->primary(['user_id', 'product_id', 'key']);	          
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_cart');
    }
};
