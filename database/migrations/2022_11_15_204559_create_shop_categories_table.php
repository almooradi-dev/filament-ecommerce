<?php

use Almooradi\FilamentEcommerce\Constants\CategoryStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	// TODO: make a dynamic tables prefix "shop_" that can be edited from a config file
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_categories', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->string('image')->nullable();
            $table->unsignedInteger('status');
            $table->string('show_in')->nullable();
            $table->foreignId('parent_category_id')->nullable()->constrained('shop_categories')->restrictOnDelete(); // "nullable()" must be before "constrained()"
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
        Schema::dropIfExists('shop_categories');
    }
};
