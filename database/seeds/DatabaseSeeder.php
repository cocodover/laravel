<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * 运行数据填充指令
     * php artisan db:seed
     */
    public function run()
    {
        //数据填充（自定义填充）
//        $this->call(UsersTableSeeder::class);
//        $this->call(RolesTableSeeder::class);

        //工厂填充（批量填充）
//        factory(App\User::class, 3)->create();
    }
}
