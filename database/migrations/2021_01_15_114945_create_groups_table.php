<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('群组所有者ID');
            $table->integer('number')->unsigned()->comment('群号');
            $table->string('groupname')->comment('群组名');
            $table->string('avatar')->comment('群组头像');
            $table->string('sign')->comment('群组简介');
            $table->tinyInteger('status')->unsigned()->comment('1审核通过 0待审核 -1审核不通过');
            $table->tinyInteger('setting')->unsigned()->comment('1直接加群 0需要验证 -1不允许加群');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groups');
    }
}
