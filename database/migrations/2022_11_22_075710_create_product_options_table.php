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
        Schema::create('product_options', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->integer("product_id")->unsigned()->nullable()->default(0);
            $table->string("title")->nullable()->default(null);
            $table->integer("type")->nullable()->default(0);
            $table->integer('status')->unsigned()->nullable()->default(1);
            $table->integer('sort')->unsigned()->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign("product_id")->references("id")->on("products")->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('product_options');
    }
};
