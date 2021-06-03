<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_videos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("event_id")->unsigned();
            $table->foreign("event_id")
            ->references("id")
            ->on("events")
            ->onDelete('cascade');
            $table->string('videoName')->nullable();
            $table->string('video');
            $table->date('videoDate')->nullable();
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
        Schema::dropIfExists('event_videos');
    }
}
