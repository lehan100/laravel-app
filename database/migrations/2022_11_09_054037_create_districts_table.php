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
        Schema::create('districts', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->string("name")->nullable()->default(null);
            $table->integer("province_id")->unsigned()->nullable()->default(0);
            $table->integer('type')->unsigned()->nullable()->default(0);
            $table->integer('status')->unsigned()->nullable()->default(0);
            $table->integer('sort')->unsigned()->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign("province_id")->references('id')->on('provinces')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('districts');
    }
};
