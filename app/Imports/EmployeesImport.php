<?php

namespace App\Imports;


use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EmployeesImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Shifts' => new ShiftSheetImport(),
        ];
    }
    
}
