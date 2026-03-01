<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::create('product_ratings', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->integer("product_id")->unsigned()->nullable()->default(0);
            $table->integer("parent_id")->unsigned()->nullable()->default(0);
            $table->string("name")->nullable()->default(null);
            $table->string("phone")->nullable()->default(null);
            $table->text("content")->nullable()->default(null);
            $table->text("images")->nullable()->default(null);
            $table->integer("rating")->nullable()->default(0);
            $table->integer("is_purchase")->unsigned()->nullable()->default(0);
            $table->integer('status')->unsigned()->nullable()->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign("product_id")->references("id")->on("products")->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('product_ratings');
    }
};
