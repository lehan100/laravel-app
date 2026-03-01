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
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->string("entype_id")->nullable()->default(null);
            $table->string("sku")->nullable()->default(null);
            $table->string("name")->nullable()->default(null);
            $table->string("name_ascii")->nullable()->default(null);
            $table->string("alias")->nullable()->default(null);
            $table->integer("quantity")->nullable()->default(0);
            $table->integer("stock")->nullable()->default(0);
            $table->integer("weight")->nullable()->default(0);
            $table->integer("price")->nullable()->default(0);
            $table->integer("special_price")->nullable()->default(0);
            $table->date("special_price_from")->nullable()->default(null);
            $table->date("special_price_to")->nullable()->default(null);
            $table->text("picture")->nullable()->default(null);
            $table->integer('status')->unsigned()->nullable()->default(0);
            $table->integer('use_coupon')->unsigned()->nullable()->default(1);
            $table->integer('sort')->unsigned()->nullable()->default(0);
            $table->integer('hit_viewer')->unsigned()->nullable()->default(0);
            $table->integer('hit_order')->unsigned()->nullable()->default(0);
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
        Schema::dropIfExists('products');
    }
};
