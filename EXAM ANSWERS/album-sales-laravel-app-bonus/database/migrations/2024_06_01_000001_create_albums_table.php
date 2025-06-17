<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlbumsTable extends Migration
{
    public function up()
    {
        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('artist_id');
            $table->integer('year');
            $table->string('name');
            $table->integer('sales');
            $table->string('album_cover_path')->nullable();
            $table->timestamps();

            $table->foreign('artist_id')->references('id')->on('artists')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('albums');
    }
}
