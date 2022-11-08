<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\User::create([
            'name'  => 'Administrator',
<<<<<<< HEAD
            'email' => 'admin@gmail.com',
            'role' => 'admin',
            'password'  => bcrypt('admin')
=======
            'email' => 'admin123@gmail.com',
            'role' => 'admin',
            'password'  => bcrypt('123456')
>>>>>>> dbe35dec87949b807d530ce49aefc1cae9f39df8
        ]);
    }
}
