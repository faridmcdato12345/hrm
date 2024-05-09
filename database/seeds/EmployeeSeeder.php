<?php

use App\Employee;
use App\Salary;
use App\Traits\ZohoTrait;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class EmployeeSeeder extends Seeder
{
    use ZohoTrait;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employee = Employee::create([
            'username'                       => 'admin',
            'firstname'                      => 'Admin',
            'lastname'                       => '',
            'contact_no'                     => '03324567833',
            'emergency_contact'              => '03324567833',
            'emergency_contact_relationship' => 'brother',
            'password'                       => bcrypt('adminlasureco2021'),
            'date_of_birth'                  => '1998-09-19',
            'designation'                    => 'admin',
            'city'                           => 'Marawi City',
            'status'                         => 0,
            'employment_status'              => 'permanent',
            'role'                           => 1,
        ]);

        $role = Role::find(1);
        $employee->assignRole($role);
    }
}
