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
        //
        Schema::create('tier_prices', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->integer('status')->unsigned()->nullable()->default(0);
            $table->date("date_from")->nullable()->default(null);
            $table->date("date_to")->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('tier_price_items', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->integer("tier_price_id")->unsigned()->nullable()->default(0);
            $table->integer('order_qty')->unsigned()->nullable()->default(0);
            $table->integer('type')->unsigned()->nullable()->default(0);
            $table->integer("special_percent")->nullable()->default(0);
            $table->integer("special_price")->nullable()->default(0);
            $table->integer('status')->unsigned()->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign("tier_price_id")->references('id')->on('tier_prices')->onDelete('cascade');
        });
        Schema::create('product_of_tier_prices', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->integer("tier_price_id")->unsigned()->nullable()->default(0);
            $table->integer("product_id")->unsigned()->nullable()->default(0);
            $table->foreign("tier_price_id")->references("id")->on("tier_prices")->onDelete('cascade');
            $table->foreign("product_id")->references("id")->on("products")->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('product_of_tier_prices');
        Schema::dropIfExists('tier_price_items');
        Schema::dropIfExists('tier_prices');
    }
};
