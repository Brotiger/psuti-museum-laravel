<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unit_photos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("unit_id")->unsigned();
            $table->foreign("unit_id")
            ->references("id")
            ->on("units")
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
        Schema::dropIfExists('unit_photos');
    }
}
