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
        Schema::create('entype_contents', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->string("entype_id")->nullable()->default(null);
            $table->text("sort_content")->nullable()->default(null);
            $table->text("content")->nullable()->default(null);
            $table->longText("title")->nullable()->default(null);
            $table->longText("keyword")->nullable()->default(null);
            $table->longText("description")->nullable()->default(null);
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
        Schema::dropIfExists('entype_contents');
    }
};
