<?php

namespace App\Imports;

use App\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class ShiftSheetImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithEvents, ShouldQueue
{
    use Importable, RegistersEventListeners,RemembersRowNumber;
    
    public function model(array $rows)
    {
        foreach ($rows as $row) 
        {
            Employee::create([
                'id_no' => $row['No'],
                'firstname' => $row['Name'],
            ]);
        }
    }
     /**
     * @return int
     */
    public function batchSize(): int
    {
        return 1000;
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 1000;
    }
    public function headingRow(): int
    {
        return 3;
    }
}
