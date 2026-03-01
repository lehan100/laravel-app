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
        Schema::create('inventory_timelines', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->integer("inventory_id")->unsigned()->nullable()->default(0);
            $table->text("comments")->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign("inventory_id")->references('id')->on('inventories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('inventory_timelines');
    }
};
