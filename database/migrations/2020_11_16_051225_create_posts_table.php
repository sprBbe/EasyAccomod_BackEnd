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
            $table->string('info_detail')->nullable();
            $table->string('detail_address');
            $table->foreignId('id_ward');
            $table->foreign('id_ward')->references('id')->on('wards');
            $table->tinyInteger('with_owner')->comment('1: live with owner; 0: no live owner');
            $table->tinyInteger('restroom')->comment('1: riêng; 0: chung');
            $table->tinyInteger('kitchen')->comment('1: có bếp; 0: không bếp');
            $table->tinyInteger('water_heater')->comment('1: có nóng lạnh; 0: không nóng lạnh');
            $table->tinyInteger('air_conditioner')->comment('1: có điều hòa; 0: không điều hòa');
            $table->tinyInteger('balcony')->comment('1: có ban công; 0: không ban công');
            $table->foreignId('id_room_type');
            $table->foreign('id_room_type')->references('id')->on('room_types');
            $table->bigInteger('square')->comment('m^2');
            $table->bigInteger('electricity_price')->comment('giá điện');
            $table->bigInteger('water_price')->comment('giá nước');
            $table->bigInteger('price')->comment('each month');
            $table->string('coordinates')->comment('Toa Do')->nullable();
            $table->foreignId('id_owner');
            $table->foreign('id_owner')->references('id')->on('users');
            $table->dateTime('time_expire')->comment('Thời điểm hết hạn của bài đăng');
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
