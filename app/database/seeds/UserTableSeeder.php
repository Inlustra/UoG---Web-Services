<?php

class UserTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('users')->delete();
        User::create(array(
            'name'     => 'Thomas Nairn',
            'username' => 'Tom',
            'email'    => 'tjnairn@gmail.com',
            'password' => Hash::make('test'),
            'validated' => true
        ));
    }

}