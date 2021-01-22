<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('number')->unique()->comment('号');
            $table->string('phone')->unique()->comment('电话');
            $table->string('username',255)->comment('昵称');
            $table->string('password',255)->comment('密码');
            $table->string('sign')->nullable();
            $table->string('avatar')->nullable();

            $table->string('birth')->nullable()->comment('生日');
            $table->string('email')->nullable()->comment('邮箱');
            $table->bigInteger('QQ')->nullable()->comment('QQ');
            $table->string('blt')->nullable()->comment('血型');
            $table->string('sex')->default('保密')->comment('性别');
            $table->string('area')->default('北京市/北京市/东城区')->comment('所在区域描述');
            $table->string('status')->default(0)->comment('offline下线 online在线');

            $table->rememberToken();
            $table->timestamps();

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
