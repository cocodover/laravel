<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecordTable extends Migration
{
    /**
     * 执行迁移命令(执行过的命令会在migrations表中生成记录,batch字段记录执行migrate次数)
     * php artisan migrate
     *
     * 查看迁移命令执行sql语句
     * php artisan migrate --pretend
     */
    public function up()
    {
        /*
         * 操作(请求)行为记录表
         * 用于记录后台用户操作行为
         */
        Schema::create('record', function (Blueprint $table) {
            $table->increments('id')->comment('序列号');
            $table->unsignedInteger('user_id')->comment('用户ID');
            $table->unsignedInteger('ip')->nullable()->comment('IP地址');
            $table->string('uri')->comment('请求URI');
            $table->string('method', 10)->comment('请求方式');
            $table->string('route', 40)->comment('路由名称');
            $table->text('request')->nullable()->comment('请求参数');
            //生成laravel维护时间相关字段(created_at&updated_at)
            $table->timestamps();

            //定义索引
            $table->index(['created_at', 'route']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('record');
    }
}
