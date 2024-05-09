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


class EmployeeList implements WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */

    private $department_id;

    public function __construct($department_id)
    {
        $this->department_id = $department_id;
    }
    
    public function registerEvents(): array
    {
        return [
            BeforeWriting::class => function(BeforeWriting $event) {
                $templateFile = new LocalTemporaryFile(storage_path('import/Employees_Details.xlsx'));
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
    // private function populateSheet($sheet)
    // {
    //     // Populate the sheet
    //     $i = 5;
    //     $employeesList = $this->query($this->department_id);
    //     foreach ($employeesList as $item) {
    //         // Get Department
    //         $dep = DB::connection('pgsql_external')->table('personnel_department')->where('id',$item->department_id)->first();
    //         $designation = DB::table('designation_sg as sg')
    //         ->join('designations as d','sg.designation_id','=','d.id')
    //         ->where('active',1)
    //         ->where('emp_id',$item->id)
    //         ->first();
    //         if($designation){
    //             $designation_name = $designation->designation_name;
    //             // Use the $designation_name variable here
    //         }else{
    //             $designation_name = '';
    //         }

    //         // dd($designation);
    //         $sheet->insertNewRowBefore($i);
    //         $sheet->setCellValue('A'.$i, $i - 4);
    //         $sheet->setCellValue('C'.$i, ucfirst($item->firstname).' '.ucfirst($item->lastname));
    //         $sheet->setCellValue('D'.$i, $designation_name);
    //         $sheet->setCellValue('E'.$i, $item->employment_status);
    //         $sheet->setCellValue('F'.$i, $designation_name);
    //         $sheet->setCellValue('G'.$i, $dep->dept_name);
    //         $sheet->setCellValue('H'.$i, $item->date_of_birth);
    //         $sheet->setCellValue('I'.$i, $item->joining_date);
    //         $sheet->setCellValue('J'.$i, $item->hiredas);
    //         $sheet->setCellValue('K'.$i, $item->gender);
    //         $sheet->setCellValue('L'.$i, $item->basic_salary);
    //         $sheet->setCellValue('M'.$i, $item->educational_attain);
    //         $sheet->setCellValue('N'.$i, $item->contact_no);
    //         $sheet->setCellValue('O'.$i, $item->email_address);
    //         $sheet->setCellValue('P'.$i, $item->sss);
    //         $sheet->setCellValue('Q'.$i, $item->philhealth);
    //         $sheet->setCellValue('R'.$i, $item->pag_ibig);
    //         $sheet->setCellValue('S'.$i, $item->emergency_contact);
    //         $sheet->setCellValue('T'.$i, $item->contact_person);
    //         $sheet->setCellValue('U'.$i, $item->emergency_contact_relationship);
    //         $sheet->setCellValue('V'.$i, $item->current_address);
    //         $sheet->setCellValue('W'.$i, $item->permanent_address);
    //         $sheet->setCellValue('X'.$i, $item->married_to);
    //         // Get Length
    //         // $columnWidthC = strlen($item->firstname.' '.$item->lastname); // Add 2 for extra padding
    //         // $columnWidthD = strlen($designation_name);
    //         // $columnWidthE = strlen($item->employment_status);
    //         // $columnWidthF = strlen($designation_name);
    //         // $columnWidthG = strlen($dep->dept_name);
    //         // $columnWidthH = strlen($item->date_of_birth);
    //         // $columnWidthI = strlen($item->joining_date);
    //         // $columnWidthJ = strlen($item->hiredas);
    //         // $columnWidthK = strlen($item->gender);
    //         // $columnWidthL = strlen($item->basic_salary);
    //         // $columnWidthM = strlen($item->educational_attain);
    //         // $columnWidthN = strlen($item->contact_no);
    //         // $columnWidthO = strlen($item->email_address);
    //         // $columnWidthP = strlen($item->sss);
    //         // $columnWidthQ = strlen($item->philhealth);
    //         // $columnWidthR = strlen($item->pag_ibig);
    //         // $columnWidthS = strlen($item->emergency_contact);
    //         // $columnWidthT = strlen($item->contact_person);
    //         // $columnWidthU = strlen($item->emergency_contact_relationship);
    //         // $columnWidthV = strlen($item->current_address);
    //         // $columnWidthW = strlen($item->permanent_address);
    //         // $columnWidthX = strlen($item->married_to);
    //         // Set Width
    //         // $sheet->getColumnDimension('C')->setWidth($columnWidthC);
    //         // $sheet->getColumnDimension('C')->setWidth($columnWidthC);
    //         // $sheet->getColumnDimension('D')->setWidth($columnWidthD);
    //         // $sheet->getColumnDimension('E')->setWidth($columnWidthE);
    //         // $sheet->getColumnDimension('F')->setWidth($columnWidthF);
    //         // $sheet->getColumnDimension('G')->setWidth($columnWidthG);
    //         // $sheet->getColumnDimension('H')->setWidth($columnWidthH);
    //         // $sheet->getColumnDimension('I')->setWidth($columnWidthI);
    //         // $sheet->getColumnDimension('J')->setWidth($columnWidthJ);
    //         // $sheet->getColumnDimension('K')->setWidth($columnWidthK);
    //         // $sheet->getColumnDimension('L')->setWidth($columnWidthL);
    //         // $sheet->getColumnDimension('M')->setWidth($columnWidthM);
    //         // $sheet->getColumnDimension('N')->setWidth($columnWidthN);
    //         // $sheet->getColumnDimension('O')->setWidth($columnWidthO);
    //         // $sheet->getColumnDimension('P')->setWidth($columnWidthP);
    //         // $sheet->getColumnDimension('Q')->setWidth($columnWidthQ);
    //         // $sheet->getColumnDimension('R')->setWidth($columnWidthR);
    //         // $sheet->getColumnDimension('S')->setWidth($columnWidthS);
    //         // $sheet->getColumnDimension('T')->setWidth($columnWidthT);
    //         // $sheet->getColumnDimension('U')->setWidth($columnWidthU);
    //         // $sheet->getColumnDimension('V')->setWidth($columnWidthV);
    //         // $sheet->getColumnDimension('W')->setWidth($columnWidthW);
    //         // $sheet->getColumnDimension('X')->setWidth($columnWidthX);
            
    //         $i++;
    //     }
    // }

    private function populateSheet($sheet)
{
    // Populate the sheet
    $i = 5;
    $employeesList = $this->query($this->department_id);
    $columnWidths = [];

    foreach ($employeesList as $item) {
        // Get Department
        $dep = DB::connection('pgsql_external')->table('personnel_department')->where('id',$item->department_id)->first();
        $designation = DB::table('designation_sg as sg')
            ->join('designations as d','sg.designation_id','=','d.id')
            ->where('active',1)
            ->where('emp_id',$item->id)
            ->first();
        if($designation){
            $designation_name = $designation->designation_name;
            // Use the $designation_name variable here
        }else{
            $designation_name = '';
        }

        $sheet->insertNewRowBefore($i);
        $sheet->setCellValue('A'.$i, $i - 4);
        $sheet->setCellValue('C'.$i, ucfirst($item->firstname).' '.ucfirst($item->lastname));
        $sheet->setCellValue('D'.$i, $designation_name);
        $sheet->setCellValue('E'.$i, $item->employment_status);
        $sheet->setCellValue('F'.$i, $designation_name);
        $sheet->setCellValue('G'.$i, $dep->dept_name);
        $sheet->setCellValue('H'.$i, $item->date_of_birth);
        $sheet->setCellValue('I'.$i, $item->joining_date);
        $sheet->setCellValue('J'.$i, $item->hiredas);
        $sheet->setCellValue('K'.$i, $item->gender);
        $sheet->setCellValue('L'.$i, $item->basic_salary);
        $sheet->setCellValue('M'.$i, $item->educational_attain);
        $sheet->setCellValue('N'.$i, $item->contact_no);
        $sheet->setCellValue('O'.$i, $item->email_address);
        $sheet->setCellValue('P'.$i, $item->sss);
        $sheet->setCellValue('Q'.$i, $item->philhealth);
        $sheet->setCellValue('R'.$i, $item->pag_ibig);
        $sheet->setCellValue('S'.$i, $item->emergency_contact);
        $sheet->setCellValue('T'.$i, $item->contact_person);
        $sheet->setCellValue('U'.$i, $item->emergency_contact_relationship);
        $sheet->setCellValue('V'.$i, $item->current_address);
        $sheet->setCellValue('W'.$i, $item->permanent_address);
        $sheet->setCellValue('X'.$i, $item->married_to);

        // Update column widths
        $columnWidths['C'] = max($columnWidths['C'] ?? 0, strlen($item->firstname.' '.$item->lastname));
        $columnWidths['D'] = max($columnWidths['D'] ?? 0, strlen($designation_name));
        $columnWidths['E'] = max($columnWidths['E'] ?? 0, strlen($item->employment_status));
        $columnWidths['F'] = max($columnWidths['F'] ?? 0, strlen($designation_name));
        $columnWidths['G'] = max($columnWidths['G'] ?? 0, strlen($dep->dept_name));
        $columnWidths['H'] = max($columnWidths['H'] ?? 0, strlen($item->date_of_birth));
        $columnWidths['I'] = max($columnWidths['I'] ?? 0, strlen($item->joining_date));
        $columnWidths['J'] = max($columnWidths['J'] ?? 0, strlen($item->hiredas));
        $columnWidths['K'] = max($columnWidths['K'] ?? 0, strlen($item->gender));
        $columnWidths['L'] = max($columnWidths['L'] ?? 0, strlen($item->basic_salary));
        $columnWidths['M'] = max($columnWidths['M'] ?? 0, strlen($item->educational_attain));
        $columnWidths['N'] = max($columnWidths['N'] ?? 0, strlen($item->contact_no));
        $columnWidths['O'] = max($columnWidths['O'] ?? 0, strlen(trim($item->email_address)));
        $columnWidths['P'] = max($columnWidths['P'] ?? 0, strlen($item->sss));
        $columnWidths['Q'] = max($columnWidths['Q'] ?? 0, strlen($item->philhealth));
        $columnWidths['R'] = max($columnWidths['R'] ?? 0, strlen($item->pag_ibig));
        $columnWidths['S'] = max($columnWidths['S'] ?? 0, strlen($item->emergency_contact));
        $columnWidths['T'] = max($columnWidths['T'] ?? 0, strlen($item->contact_person));
        $columnWidths['U'] = max($columnWidths['U'] ?? 0, strlen($item->emergency_contact_relationship));
        $columnWidths['V'] = max($columnWidths['V'] ?? 0, strlen($item->current_address));
        $columnWidths['W'] = max($columnWidths['W'] ?? 0, strlen($item->permanent_address));
        $columnWidths['X'] = max($columnWidths['X'] ?? 0, strlen($item->married_to));

        $i++;
    }

    // Set column width
    foreach ($columnWidths as $column => $width) {
        $currentWidth = $sheet->getColumnDimension($column)->getWidth();
        if ($width > $currentWidth) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
    }
}

    public function query($depID){
        
        $employee = collect(DB::table('employees as e')
        ->where('department_id',$depID)
        ->get());

        
        return $employee;

    }

    

    
}
