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
        Schema::create('media_positions', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->string("name")->nullable()->default(null);
            $table->string("code")->nullable()->default(null);
            $table->integer("mode")->unsigned()->nullable()->default(0);
            $table->integer('status')->unsigned()->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('media_banners', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->string("name")->nullable()->default(null);
            $table->integer("position_id")->unsigned()->nullable()->default(0);
            $table->integer("category_id")->unsigned()->nullable()->default(0);
            $table->string("alias_link")->nullable()->default(null);
            $table->string("picture")->nullable()->default(null);
            $table->text("sort_content")->nullable()->default(null);
            $table->integer('status')->unsigned()->nullable()->default(0);
            $table->integer('sort')->unsigned()->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign("position_id")->references('id')->on('media_positions')->onDelete('cascade');
            $table->foreign("category_id")->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('media_positions');
        Schema::dropIfExists('media_banners');
    }
};
