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
        Schema::create('buyables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedBigInteger('discount_price')->default(0);
            $table->timestamp('availabled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('buyables');
    }
};
