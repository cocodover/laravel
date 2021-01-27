<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * 数据填充逻辑
     * php artisan db:seed --class=UsersTableSeeder
     */
    public function run()
    {
        $name = str_random(10);//laravel辅助函数随机字符串
        //此写法不会自动维护时间戳
        DB::table('users')->insert([
            'name' => $name,
            'email' => $name . '@gmail.com',
            'password' => bcrypt('secret'),//对密码字段进行加密(验证密码使用Hash::check)
        ]);
    }
}
