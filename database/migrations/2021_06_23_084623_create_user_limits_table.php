<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLimitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_limits', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('empLimit')->default(1);
            $table->integer('heroLimit')->default(0);
            $table->integer('unitLimit')->default(0);
            $table->integer('graduateLimit')->default(0);
            $table->integer('eventLimit')->default(0);
            $table->bigInteger("user_id")->unsigned();
            $table->foreign("user_id")
            ->references("id")
            ->on("users")
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_limits');
    }
}
