<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $designations = [
            ['designation_name' => 'General Manager'],
        ];
        DB::table('designations')->insert($designations);
    }
}
