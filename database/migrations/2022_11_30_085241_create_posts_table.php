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
        Schema::create('posts', function (Blueprint $table) {
           $table->increments('id')->autoIncrement()->primary();
           $table->integer("category_id")->unsigned()->nullable()->default(0);
           $table->string("entype_id")->nullable()->default(null);
           $table->string("name")->nullable()->default(null);
           $table->string("alias")->nullable()->default(null);
           $table->string("picture")->nullable()->default(null);
           $table->integer('sort')->unsigned()->nullable()->default(0);
           $table->integer('hit_viewer')->unsigned()->nullable()->default(0);
           $table->integer('status')->unsigned()->nullable()->default(0);
           $table->timestamps();
           $table->softDeletes();
           $table->foreign("category_id")->references("id")->on("categories")->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
};
