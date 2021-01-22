<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->integer('id')->comment('群id');
            $table->string('avatar')->nullable()->comment('群头像');
            $table->string('groupname')->comment('群名');
            $table->string('sign')->nullable()->comment('群简介');
            $table->integer('user_id')->comment('群员id');
            $table->string('user_avatar')->nullable()->comment('群员头像');
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
        Schema::dropIfExists('members');
    }
}
