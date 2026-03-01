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
        Schema::create('order_payments', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->integer("order_id")->unsigned()->nullable()->default(0);
            $table->string("payment_code")->nullable()->default(null);
            $table->string("payment_name")->nullable()->default(null);
            $table->text("history")->nullable()->default(null);
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
        Schema::dropIfExists('order_payments');
    }
};
