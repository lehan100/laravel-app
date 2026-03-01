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
        Schema::create('provinces', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->string("name")->nullable()->default(null);
            $table->integer('status')->unsigned()->nullable()->default(0);
            $table->integer('type')->unsigned()->nullable()->default(0);
            $table->integer('sort')->unsigned()->nullable()->default(0);
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
        Schema::dropIfExists('provinces');
    }
};
