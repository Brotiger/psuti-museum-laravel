<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGraduatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('graduates', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("addUserId")->unsigned()->nullable();
            $table->foreign("addUserId")
            ->references("id")
            ->on("users");
            $table->string('documentName')->nullable();
            $table->string('documentType')->nullable();
            $table->string('documentStatus')->nullable();
            $table->boolean('confirmLoss')->nullable();
            $table->boolean('confirmSwap')->nullable();
            $table->boolean('confirmDelete')->nullable();
            $table->string('educationLevel')->nullable();
            $table->string('series')->nullable();
            $table->string('number')->nullable();
            $table->date('issueDate')->nullable();
            $table->string('registrationNumber')->unique()->required();
            $table->string('specialtyCode')->nullable();
            $table->string('specialtyName')->nullable();
            $table->string('qualificationName')->nullable();
            $table->string('enteredYear')->nullable();
            $table->string('exitYear')->nullable();
            $table->Integer('trainingPeriod')->nullable();
            $table->string('lastName')->required();
            $table->string('firstName')->required();
            $table->string('secondName')->nullable();
            $table->date('dateBirthday')->nullable();
            $table->string('sex')->nullable();
            $table->string('citizenship')->nullable();
            $table->string('educationForm')->nullable();
            $table->boolean('first')->nullable();
            $table->string('fundingSource')->nullable();
            $table->string('snills')->nullable();
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
        Schema::dropIfExists('graduates');
    }
}
