<?php

use App\Media;
use Illuminate\Database\Seeder;

class MediaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Media::insert([
            [
                'name'          =>  'a',
                'description'   =>  'b',
                'album_id'      =>  null,
                'cloud_id'      =>  'a7sdkpputkcjxkuyoftj',
                'created_at'    =>  new DateTime,
                'updated_at'    =>  new DateTime,
            ],

            [
                'name'          =>  'c',
                'description'   =>  'd',
                'album_id'      =>  null,
                'cloud_id'      =>  'wxgnrawgisszzguwbabw',
                'created_at'    =>  new DateTime,
                'updated_at'    =>  new DateTime,
            ],

            [
                'name'          =>  'e',
                'description'   =>  'f',
                'album_id'      =>  null,
                'cloud_id'      =>  'uqqraelexucskrtkrah0',
                'created_at'    =>  new DateTime,
                'updated_at'    =>  new DateTime,
            ],

            [
                'name'          =>  'g',
                'description'   =>  'h',
                'album_id'      =>  null,
                'cloud_id'      =>  'inect6rktm5phdjbdmks',
                'created_at'    =>  new DateTime,
                'updated_at'    =>  new DateTime,
            ],

            [
                'name'          =>  'i',
                'description'   =>  'j',
                'album_id'      =>  null,
                'cloud_id'      =>  'vksxmnrwyzrdqztjrbeb',
                'created_at'    =>  new DateTime,
                'updated_at'    =>  new DateTime,
            ],

            [
                'name'          =>  'k',
                'description'   =>  'l',
                'album_id'      =>  null,
                'cloud_id'      =>  '',
                'created_at'    =>  new DateTime,
                'updated_at'    =>  new DateTime,
            ],

            [
                'name'          =>  'm',
                'description'   =>  'n',
                'album_id'      =>  null,
                'cloud_id'      =>  'neanluun90p8gqb8s2ah',
                'created_at'    =>  new DateTime,
                'updated_at'    =>  new DateTime,
            ],

            [
                'name'          =>  'o',
                'description'   =>  'p',
                'album_id'      =>  null,
                'cloud_id'      =>  'ylt5y8bwntb4w6tm7o6j',
                'created_at'    =>  new DateTime,
                'updated_at'    =>  new DateTime,
            ],

            [
                'name'          =>  'q',
                'description'   =>  'r',
                'album_id'      =>  null,
                'cloud_id'      =>  'ewmgc0mnwsscgamu660y',
                'created_at'    =>  new DateTime,
                'updated_at'    =>  new DateTime,
            ],

            [
                'name'          =>  's',
                'description'   =>  't',
                'album_id'      =>  null,
                'cloud_id'      =>  'qicufuyvspldqg6noxiq',
                'created_at'    =>  new DateTime,
                'updated_at'    =>  new DateTime,
            ],

            [
                'name'          =>  'u',
                'description'   =>  'v',
                'album_id'      =>  null,
                'cloud_id'      =>  'b41poy63yrijgymgwtpy',
                'created_at'    =>  new DateTime,
                'updated_at'    =>  new DateTime,
            ],
        ]);
    }
}
