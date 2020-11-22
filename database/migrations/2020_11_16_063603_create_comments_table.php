<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();            
            $table->string('content');
            $table->tinyInteger('status')->comment('1: checked; 0: unchecked');
            $table->tinyInteger('rate')->comment('1-5 stars');
            $table->foreignId('id_from');
            $table->foreign('id_from')->references('id')->on('users');
            $table->foreignId('id_post');
            $table->foreign('id_post')->references('id')->on('posts'); 
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
        Schema::dropIfExists('comments');
    }
}
