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
        Schema::create('coupon_codes', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->string("name")->nullable()->default(null);
            $table->string("coupon_code")->nullable()->default(null);
            $table->integer('type')->unsigned()->nullable()->default(0);
            $table->integer('uses')->unsigned()->nullable()->default(0);
            $table->integer('max_uses_user')->unsigned()->nullable()->default(0);
            $table->integer("discount_amount")->nullable()->default(0);
            $table->integer("discount_amount_from")->nullable()->default(0);
            $table->integer("discount_max")->nullable()->default(0);
            $table->date("date_from")->nullable()->default(null);
            $table->date("date_to")->nullable()->default(null);
            $table->integer('status')->unsigned()->nullable()->default(0);
            $table->integer('is_public')->unsigned()->nullable()->default(0);
            $table->integer('is_product_use_coupon')->unsigned()->nullable()->default(0);
            $table->integer('is_check_product')->unsigned()->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('product_coupon_codes', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->integer("coupon_code_id")->unsigned()->nullable()->default(0);
            $table->integer("category_id")->unsigned()->nullable()->default(0);
            $table->integer("product_id")->unsigned()->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign("coupon_code_id")->references('id')->on('coupon_codes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_coupon_codes');
        Schema::dropIfExists('coupon_codes');
    }
};
