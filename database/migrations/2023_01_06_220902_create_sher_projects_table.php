<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sher_projects', function (Blueprint $table) {
            $table->id();
            $table->string('rate');
            //-------------------------------
            $table->bigInteger('project_id')->unsigned()->index(); // this is working
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            //-------------------------------
            $table->bigInteger('owner_id')->unsigned()->index();
            $table->foreign('owner_id')->references('user_id')->on('projects')->onDelete('cascade');
            //-------------------------------
            $table->string('title');
            $table->string('sher-type');
            $table->string('description');
            $table->string('from');
            $table->string('classification');




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
        Schema::dropIfExists('sher_projects');
    }
};
