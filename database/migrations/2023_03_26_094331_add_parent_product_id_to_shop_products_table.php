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
        Schema::table('shop_products', function (Blueprint $table) {
            $table->foreignId('parent_product_id')->nullable()->after('id')->constrained('shop_products')->cascadeOnDelete();

            $table->index('parent_product_id');
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
            $table->dropForeign(['parent_product_id']);
            $table->dropIndex(['parent_product_id']);
            $table->dropColumn('parent_product_id');
        });
    }
};
