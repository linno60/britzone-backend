<?php

use Illuminate\Database\Seeder;
use App\Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::insert([
            [//1
                'name'              =>  'Classes',
                'type'              =>  'p', //post
                'icon'              =>  'fa-graduation-cap',
                'category_id'       =>  null,
                'category_relation' =>  'one',
                'created_at'        =>  new DateTime,
                'updated_at'        =>  new DateTime,
            ],
            [ //2
                'name'              =>  'Comperhensive Learning Program',
                'type'              =>  'p',
                'icon'              =>  '',
                'category_id'       =>  1,
                'category_relation' =>  null,
                'created_at'        =>  new DateTime,
                'updated_at'        =>  new DateTime,
            ],
            [//3
                'name'              =>  'Britzone Speaking Academy',
                'type'              =>  'p',
                'icon'              =>  '',
                'category_id'       =>  1,
                'category_relation' =>  null,
                'created_at'        =>  new DateTime,
                'updated_at'        =>  new DateTime,
            ],
            [//4
                'name'              =>  'Britzone Fun Day',
                'type'              =>  'p',
                'icon'              =>  '',
                'category_id'       =>  1,
                'category_relation' =>  null,
                'created_at'        =>  new DateTime,
                'updated_at'        =>  new DateTime,
            ],

            /*
            [//5
                'name'              => 'How do you know Britzone?',
                'type'              =>  'a', //attendence,
                'icon'              =>  '',
                'category_id'       =>  null,
                'category_relation' =>  'many',
                'created_at'        =>  new DateTime,
                'updated_at'        =>  new DateTime,
            ],
            [//6
                'name'              =>  'From a friend',
                'type'              =>  'a',
                'icon'              =>  '',
                'category_id'       =>  5,
                'category_relation' =>  null,
                'created_at'        =>  new DateTime,
                'updated_at'        =>  new DateTime,
            ],

            */


            /*
            [//2
                'name'              =>  'Events',
                'type'              =>  'p',
                'icon'              =>  'fa-calendar',
                'category_id'       =>  null,
                'category_relation' =>  'many',
                'created_at'        =>  new DateTime,
                'updated_at'        =>  new DateTime,
            ],
            [//3
                'name'              =>  'News',
                'type'              =>  'p',
                'icon'              =>  'fa-rss',
                'category_id'       =>  null,
                'category_relation' =>  'many',
                'created_at'        =>  new DateTime,
                'updated_at'        =>  new DateTime,
            ],
            [//4
                'name'              =>  'Announcements',
                'type'              =>  'p',
                'icon'              =>  'fa-bullhorn',
                'category_id'       =>  null,
                'category_relation' =>  'many',
                'created_at'        =>  new DateTime,
                'updated_at'        =>  new DateTime,
            ],
            */
        ]);
    }
}
