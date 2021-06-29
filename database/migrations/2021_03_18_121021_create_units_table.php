<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string("fullUnitName")->unique();
            $table->string("shortUnitName")->nullable();
            $table->date("creationDate")->nullable();
            $table->bigInteger("addUserId")->unsigned()->nullable();
            $table->foreign("addUserId")
            ->references("id")
            ->on("users")
            ->onDelete("set null");
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
        Schema::dropIfExists('units');
    }
}
