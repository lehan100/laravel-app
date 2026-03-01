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

        Schema::create('product_option_entries', function (Blueprint $table) {
           $table->increments('id')->autoIncrement()->primary();
            $table->string("title")->nullable()->default(null);
            $table->integer("type")->nullable()->default(0);
            $table->integer('status')->unsigned()->nullable()->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
         Schema::table('product_option_attributes', function (Blueprint $table) {
            //$table->foreign("product_entries_id")->references("id")->on("product_option_entries")->onDelete('cascade');
          // $table->foreign("product_entries_id")->references("id")->on("product_options")->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_option_entries');
    }
};
