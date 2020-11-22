<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('id_near_places');
            $table->foreign('id_near_places')->references('id')->on('near_places');  
            $table->tinyInteger('with_owner')->comment('1: live with owner; 0: no live owner');
            $table->foreignId('id_room_types');
            $table->foreign('id_room_types')->references('id')->on('room_types');
            $table->bigInteger('square')->comment('m^2');
            $table->bigInteger('price');   
            $table->string('coordinates')->comment('Toa Do');;
            $table->foreignId('id_owner');
            $table->foreign('id_owner')->references('id')->on('users');
            $table->mediumInteger('time_display')->comment('days');
            $table->bigInteger('views');
            $table->tinyInteger('status')->comment('1: checked; 0: unchecked');
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
        Schema::dropIfExists('posts');
    }
}
