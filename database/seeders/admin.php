<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class admin extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\admin::create([
            "name" => "admin",
            "email" => "admin@gmail.com",
            'password' => Hash::make('123456789')
        ]);
    }
}
