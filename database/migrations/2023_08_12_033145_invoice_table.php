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
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->string("invoice_id")->nullable()->default(null);
            $table->string("name")->nullable()->default(null);
            $table->string("gender")->nullable()->default(null);
            $table->string("phone")->nullable()->default(null);
            $table->string("email")->nullable()->default(null);
            $table->integer("city_id")->unsigned()->nullable()->default(0);
            $table->integer("district_id")->unsigned()->nullable()->default(0);
            $table->integer("ward_id")->unsigned()->nullable()->default(0);
            $table->string("address")->nullable()->default(null);
            $table->string("note")->nullable()->default(null);
            $table->integer("price_total")->nullable()->default(0);
            $table->integer("price_shipping")->nullable()->default(0);
            $table->integer("price_discount")->nullable()->default(0);
            $table->integer("viewer")->nullable()->default(0);
            $table->string('order_status')->nullable()->default('awaiting');
            $table->string('shipping_status')->nullable()->default('awaiting');
            $table->string('payment_status')->nullable()->default('awaiting');
            $table->string('payment_method')->nullable()->default('cash_on_delivery');
            $table->string("coupon_code")->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->integer("order_id")->unsigned()->nullable()->default(0);
            $table->integer("product_id")->unsigned()->nullable()->default(0);
            $table->string("name")->nullable()->default(null);
            $table->string("sku")->nullable()->default(null);
            $table->integer("qty")->nullable()->default(0);
            $table->integer("weight")->nullable()->default(0);
            $table->integer("price")->nullable()->default(0);
            $table->integer("special_price")->nullable()->default(0);
            $table->date("special_price_from")->nullable()->default(null);
            $table->date("special_price_to")->nullable()->default(null);
            $table->text("options")->nullable()->default(null);
            $table->text("option_entries")->nullable()->default(null);
            $table->string("path")->nullable()->default(null);
            $table->string("picture")->nullable()->default(null);
            $table->text("gift")->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign("order_id")->references('id')->on('orders')->onDelete('cascade');
        });
        Schema::create('order_timelines', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->integer("order_id")->unsigned()->nullable()->default(0);
            $table->text("comments")->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign("order_id")->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('order_timelines');
    }
};
