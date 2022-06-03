<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 0; $i < 10 ; $i++) {
            DB::table('posts')->insert([
                'title' => Str::random(10),
                'content' => Str::random(10).'@gmail.com',
                'author' =>  Str::random(10),
            ]);
        }
    }
}
