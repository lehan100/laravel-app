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
        Schema::create('product_attribute_sets', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->string("alias")->nullable()->default(null);
            $table->integer("product_id")->unsigned()->nullable()->default(0);
            $table->string("attribute_set_ids")->nullable()->default(null);
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
    public function down()
    {
        Schema::dropIfExists('product_attribute_sets');
    }
};
