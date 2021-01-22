<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('records', function (Blueprint $table) {
            $table->integer('from_id')->unsigned()->comment('消息来源者id');
            $table->string('from_name')->comment('消息来源者名字');
            $table->string('from_avatar')->comment('消息来源者头像');
            $table->integer('to_id')->unsigned()->comment('消息来源者id');
            $table->text('content')->nullable();
            $table->tinyInteger('push')->unsigned()->comment('是否已推送？ 0已推送 1尚未推送');
            $table->string('type')->comment('消息类型');
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
        Schema::dropIfExists('records');
    }
}
