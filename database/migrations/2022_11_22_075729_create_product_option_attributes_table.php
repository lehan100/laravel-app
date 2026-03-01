<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('product_option_attributes', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->integer("option_id")->unsigned()->nullable()->default(0);
            $table->integer("product_entries_id")->unsigned()->nullable()->default(0);
            $table->string("sku")->nullable()->default(null);
            $table->string("title")->nullable()->default(null);
            $table->integer("price")->nullable()->default(0);
            $table->integer("price")->nullable()->default(0);
            $table->integer("special_price")->nullable()->default(0);
            $table->date("special_price_from")->nullable()->default(null);
            $table->date("special_price_to")->nullable()->default(null);
            $table->string("picture")->nullable()->default(null);
            $table->string("color")->nullable()->default(null);
            $table->integer('status')->unsigned()->nullable()->default(1);
            $table->integer('sort')->unsigned()->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
            //$table->foreign("option_id")->references("id")->on("product_options")->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('product_option_attributes');
    }
};
