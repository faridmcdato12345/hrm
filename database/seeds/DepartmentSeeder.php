<?php

use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Department::Create([
            'department_name'      => 'Institutional Services Department',
            'status'               => 'Active',
        ]);
        App\Department::Create([
            'department_name'      => 'Corporate Planning Department',
            'status'               => 'Active',
        ]);
        App\Department::Create([
            'department_name'      => 'Technical Services Department',
            'status'               => 'Active',
        ]);
        App\Department::Create([
            'department_name'      => 'Financial Services Department',
            'status'               => 'Active',
        ]);
        App\Department::Create([
            'department_name'      => 'Internal Audit Department',
            'status'               => 'Active',
        ]);
        App\Department::Create([
            'department_name'      => 'Office of General Manager',
            'status'               => 'Active',
        ]);
        App\Department::Create([
            'department_name'      => 'Area Services Department',
            'status'               => 'Active',
        ]);
    }
}
