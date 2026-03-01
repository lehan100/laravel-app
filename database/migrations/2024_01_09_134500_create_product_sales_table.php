<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_sales', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->string("name")->nullable()->default(null);
            $table->string("alias")->nullable()->default(null);
            $table->text("decsription")->nullable()->default(null);
            $table->integer('is_homepage')->unsigned()->nullable()->default(0);
            $table->integer('status')->unsigned()->nullable()->default(0);
            $table->date("date_from")->nullable()->default(null);
            $table->date("date_to")->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('product_sale_items', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->integer("product_sales_id")->unsigned()->nullable()->default(0);
            $table->integer("product_id")->unsigned()->nullable()->default(0);
            $table->integer('quantity_is_uses_product')->unsigned()->nullable()->default(0);
            $table->integer('order_qty')->unsigned()->nullable()->default(0);
            $table->integer("special_percent")->nullable()->default(0);
            $table->integer("special_price")->nullable()->default(0);
            $table->integer("buy_qty")->nullable()->default(0);
            $table->integer("gift_qty")->nullable()->default(0);
            $table->string("gift_sku")->nullable()->default(null);
            $table->text("gift_sku_info")->nullable()->default(null);
            $table->date("date_from")->nullable()->default(null);
            $table->date("date_to")->nullable()->default(null);
            $table->integer('status')->unsigned()->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign("product_sales_id")->references('id')->on('product_sales')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_sale_items');
        Schema::dropIfExists('product_sales');
    }
};
