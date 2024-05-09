<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleSeeder::class); //make it before employees
        $this->call(EmployeeSeeder::class);
        //$this->call(LeaveTypeSeeder::class);
        //$this->call(DesignationSeeder::class);
        //$this->call(DepartmentSeeder::class);
    }
}
