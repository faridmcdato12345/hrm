<?php

namespace App\Exports;

use App\Employee;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Files\LocalTemporaryFile;
use Maatwebsite\Excel\Excel;


class EmployeeLeave implements WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */

    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
    
    public function registerEvents(): array
    {
        return [
            BeforeWriting::class => function(BeforeWriting $event) {
                $templateFile = new LocalTemporaryFile(storage_path('import/Application_Leave.xlsx'));
                // $templateFile = new LocalTemporaryFile(storage_path('app/public/files/mytemplate.xlsx'))
                // dd($templateFile);
                $event->writer->reopen($templateFile, Excel::XLSX);
                $sheet = $event->writer->getSheetByIndex(0);

                $this->populateSheet($sheet);
                
                $event->writer->getSheetByIndex(0)->export($event->getConcernable()); // call the export on the first sheet

                return $event->getWriter()->getSheetByIndex(0);
            },
        ];
    }

    private function populateSheet($sheet)
    {
        // Populate the sheet
        $employee = $this->query();
        $dep = DB::connection('pgsql_external')->table('personnel_department')->where('id',$employee['department_id'])->first();
        $from = Carbon::parse($employee['datefrom']);
        $to = Carbon::parse($employee['dateto']);
        $differenceInDays = $from->diffInDays($to);

        $sheet->setCellValue('B11', ucfirst($employee['firstname']).' '.ucfirst($employee['lastname']));
        $sheet->setCellValue('H11', $employee['designation_name']);
        $sheet->setCellValue('M11', number_format($employee['basic_salary'],2));
        $sheet->setCellValue('B12', $dep->dept_name);
        $sheet->setCellValue('I12', $employee['joining_date']);
        $sheet->setCellValue('D13', $differenceInDays);
        $sheet->setCellValue('G13', $from->format('M d, Y'));
        $sheet->setCellValue('J13', $to->format('M d, Y'));
        $sheet->setCellValue('A16', '☐');
        $sheet->setCellValue('F16', '☐');
        $sheet->setCellValue('J16', '☐');
        $sheet->setCellValue('A18', '☐');
        $sheet->setCellValue('F18', '☐');
        $sheet->setCellValue('J18', '☐');
       
    }


    public function query(){
        
        $employee = collect(DB::table('employees as e')
        ->join('leaves as l','e.id','=','l.employee_id')
        ->leftJoin('designations as d','e.designation_id','=','d.id')
        ->where('e.id',$this->id)
        ->first());

        
        return $employee;

    }

    

    
}
