<?php

use Illuminate\Database\Seeder;
use App\Attendable;

class AttendableTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Attendable::insert([
            [
                'has_date_start'    =>  true,
                'has_date_end'      =>  false,
                'has_time_start'    =>  true,
                'has_time_end'      =>  true,
                'attendable_id'     =>  '1',
                'attendable_type'   =>  'App\\Category',
            ],
        ]);
    }
}
