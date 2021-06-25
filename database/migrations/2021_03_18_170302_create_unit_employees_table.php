<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unit_employees', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("unit_id")->unsigned();
            $table->foreign("unit_id")
            ->references("id")
            ->on("units")
            ->onDelete('cascade');
            $table->bigInteger("employee_id")->unsigned();
            $table->foreign("employee_id")
            ->references("id")
            ->on("employees")
            ->onDelete('cascade');
            $table->string('post')->nullable();
            $table->date('recruitmentDate')->nullable();
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
        Schema::dropIfExists('unit_employees');
    }
}
