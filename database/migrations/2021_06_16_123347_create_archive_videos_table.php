<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchiveVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archive_videos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger("page_id")->unsigned();
            $table->foreign("page_id")
            ->references("id")
            ->on("pages");
            $table->string('videoName')->nullable();
            $table->string('video');
            $table->date('videoDate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('archive_videos');
    }
}
