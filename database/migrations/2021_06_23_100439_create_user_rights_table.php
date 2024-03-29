<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_rights', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->boolean('root')->default(false);
            $table->date('empAdmin')->nullable();
            $table->date('heroAdmin')->nullable();
            $table->date('unitAdmin')->nullable();
            $table->date('eventAdmin')->nullable();
            $table->date('pageAdmin')->nullable();
            $table->date('graduateAdmin')->nullable();
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
        Schema::dropIfExists('user_rights');
    }
}
