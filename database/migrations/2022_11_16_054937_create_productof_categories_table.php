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
        Schema::create('product_of_categories', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->integer("category_id")->unsigned()->nullable()->default(0);
            $table->integer("product_id")->unsigned()->nullable()->default(0);
            $table->foreign("category_id")->references("id")->on("categories")->onDelete('cascade');
            $table->foreign("product_id")->references("id")->on("products")->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('product_of_categories');
    }
};
