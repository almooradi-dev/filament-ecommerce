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
        Schema::create('shop_products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('sku')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('long_description')->nullable();
            $table->integer('gender')->nullable();
            $table->string('show_in')->nullable();
            $table->unsignedInteger('status')->default(ProductStatus::PENDING);
            $table->longText('media_files')->nullable();
            
            $table->string('highlight_label_background_color')->nullable();
            $table->string('highlight_label_text_color')->nullable();
            $table->string('highlight_label_text')->nullable();
            
            $table->string('quantity')->nullable();
            $table->string('price')->nullable();
            $table->string('discount_type')->nullable();
            $table->string('discount_amount')->nullable();
            
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
        Schema::dropIfExists('shop_products');
    }
};
