<?php

use App\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = new Role([
            'id' => 1,
            'name' => '测试角色',
            'permission' => ['test.permission']
        ]);
        $role->save();
    }
}
