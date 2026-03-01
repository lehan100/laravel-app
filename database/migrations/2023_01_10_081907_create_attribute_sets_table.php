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
        Schema::create('attribute_sets', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->string("name")->nullable()->default(null);
            $table->string("alias")->nullable()->default(null);
            $table->integer("type")->nullable()->default(0);
            $table->integer('status')->unsigned()->nullable()->default(1);
            $table->integer('sort')->unsigned()->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('attribute_set_values', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->integer("attribute_set_id")->unsigned()->nullable()->default(0);
            $table->string("name")->nullable()->default(null);
            $table->string("alias")->nullable()->default(null);
            $table->string("picture")->nullable()->default(null);
            $table->string("color")->nullable()->default(null);
            $table->integer('sort')->unsigned()->nullable()->default(0);
            $table->integer('status')->unsigned()->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign("attribute_set_id")->references("id")->on("attribute_sets")->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attribute_sets');
        Schema::dropIfExists('attribute_set_values');
    }
};
