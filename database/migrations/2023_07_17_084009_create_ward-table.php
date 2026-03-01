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
        Schema::create('wards', function (Blueprint $table) {
            $table->increments('id')->autoIncrement()->primary();
            $table->string("name")->nullable()->default(null);
            $table->integer("district_id")->unsigned()->nullable()->default(0);
            $table->integer('status')->unsigned()->nullable()->default(0);
            $table->integer('type')->unsigned()->nullable()->default(0);
            $table->integer('sort')->unsigned()->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign("district_id")->references('id')->on('districts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('wards');
    }
};
