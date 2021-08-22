<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeroPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hero_photos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("hero_id")->unsigned();
            $table->foreign("hero_id")
            ->references("id")
            ->on("heroes")
            ->onDelete('cascade');
            $table->string('photoName')->nullable();
            $table->string('photo');
            $table->date('photoDate')->nullable();
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
        Schema::dropIfExists('hero_photos');
    }
}
