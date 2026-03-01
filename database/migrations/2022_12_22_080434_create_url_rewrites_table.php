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
        Schema::create('url_rewrites', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->string("path")->nullable()->default(null);
            $table->string("route")->nullable()->default(null);
            $table->integer("category_id")->nullable()->default(0);
            $table->integer("product_id")->nullable()->default(0);
            $table->string("post_id")->nullable()->default(0);
            $table->string("sale_id")->nullable()->default(0);
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
        Schema::dropIfExists('url_rewrites');
    }
};
