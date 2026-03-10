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
        if (!Schema::hasTable('product_of_option_entries')) {
        Schema::create('product_of_option_entries', function (Blueprint $table) {
            $table->increments('id'); 

            $table->integer("product_option_entries_id")->unsigned()->nullable();
            $table->integer("product_id")->unsigned()->nullable();

            $table->integer("order")->unsigned()->nullable()->default(0);

            $table->foreign("product_option_entries_id", "fk_product_option_entries")
                ->references("id")
                ->on("product_option_entries")
                ->onDelete('cascade');

            $table->foreign("product_id")
                ->references("id")
                ->on("products")
                ->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_of_option_entries');
    }
};
