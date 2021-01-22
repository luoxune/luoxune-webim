<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->string('content')->comment('消息内容');
            $table->integer('uid')->unsigned()->comment('接收人');
            $table->integer('from')->nullable()->unsigned()->comment('发送人');
            $table->integer('from_group')->nullable()->unsigned()->comment('组 0 friend 1 group_id');
            $table->tinyInteger('type')->unsigned()->comment('type 0 friend 1 group 2 system,');
            $table->tinyInteger('read')->unsigned()->default(0)->comment('是否已阅读');
            $table->tinyInteger('agree')->unsigned()->default(0)->comment('是否已同意 0 未确定 1 已同意 2 已拒绝');
            $table->string('remark')->nullable()->comment('附言');
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
        Schema::dropIfExists('messages');
    }
}
