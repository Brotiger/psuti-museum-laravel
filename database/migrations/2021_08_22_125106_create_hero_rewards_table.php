<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeroRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hero_rewards', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("hero_id")->unsigned();
            $table->foreign("hero_id")
            ->references("id")
            ->on("heroes")
            ->onDelete('cascade');
            $table->string('reward');
            $table->date('rewardDate')->nullable();
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
        Schema::dropIfExists('hero_rewards');
    }
}
