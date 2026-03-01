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
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->string("entype_id")->nullable(true)->default(null);
            $table->string('name')->nullable(true)->default(null);
            $table->string('alias')->nullable(true)->default(null);
            $table->integer("parent_id")->unsigned()->nullable(true)->default(0);
            $table->integer('page')->unsigned()->nullable(true)->default(0);
            $table->integer('status')->unsigned()->nullable(true)->default(0);
            $table->integer('sort')->unsigned()->nullable(true)->default(0);
            $table->integer('position_menu')->unsigned()->nullable(true)->default(0);
            $table->integer('position_top')->unsigned()->nullable(true)->default(0);
            $table->integer('position_main')->unsigned()->nullable(true)->default(0);
            $table->integer('position_footer_a')->unsigned()->nullable(true)->default(0);
            $table->integer('position_footer_b')->unsigned()->nullable(true)->default(0);
            $table->string("picture")->nullable(true)->default(null);
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
        Schema::dropIfExists('categories');
    }
};
