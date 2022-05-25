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
        Schema::create('related_contents', function (Blueprint $table) {
            $table->foreignId('content_id')->constrained('contents');
            $table->foreignId('related_id')->constrained('contents');
            $table->timestamps();
            $table->primary(['content_id', 'related_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('related_contents');
    }
};
