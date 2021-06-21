<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("page_id")->unsigned();
            $table->foreign("page_id")
            ->references("id")
            ->on("pages")
            ->onDelete('cascade');
            $table->bigInteger("addUserId")->unsigned()->nullable();
            $table->foreign("addUserId")
            ->references("id")
            ->on("users");
            $table->text('comment');
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
        Schema::dropIfExists('histories');
    }
}
