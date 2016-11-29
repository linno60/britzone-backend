<?php

use Illuminate\Database\Seeder;
use App\User;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name'          =>  'Administrator',
            'email'         =>  'britzone.jkt@gmail.com',
            'password'      =>  Hash::make('12345'),
            'created_at'    =>  new DateTime,
            'updated_at'    =>  new DateTime
        ]);
    }
}
