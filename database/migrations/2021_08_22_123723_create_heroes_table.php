<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeroesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('heroes', function (Blueprint $table) {
            $table->id();
            $table->string("img")->nullable();
            $table->string("firstName");
            $table->string("lastName");
            $table->string("secondName")->nullable();
            $table->bigInteger("addUserId")->unsigned()->nullable();
            $table->foreign("addUserId")
            ->references("id")
            ->on("users")
            ->onDelete("set null");
            $table->string("dateBirthday")->nullable();
            $table->text("description")->nullable();
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
        Schema::dropIfExists('heroes');
    }
}
