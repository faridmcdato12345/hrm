<?php

namespace App\Exports;

use App\Employee;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Files\LocalTemporaryFile;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EmployeeEarnedLeave implements WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */

    private $id;
    private $year;

    public function __construct($id,$year)
    {
        $this->id = $id;
        $this->year = $year;
    }
    
    public function registerEvents(): array
    {
        return [
            BeforeWriting::class => function(BeforeWriting $event) {
                $templateFile = new LocalTemporaryFile(storage_path('import/Earned_Leave.xlsx'));
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
        $sheet->setCellValue('B11', $this->year);
        $sheet->setCellValue('G7', ucfirst($employee['firstname']).' '.ucfirst($employee['lastname']));
        $sheet->setCellValue('G8', $employee['designation_name']);
        $sheet->setCellValue('G9', ($employee['joining_date'] != NULL) ? Carbon::parse($employee['joining_date'])->format('M d, Y') : '');
        $sheet->setCellValue('AE7', 'TBD');
        $sheet->setCellValue('AE8', $employee['employment_status']);
        $sheet->setCellValue('AE9', $this->year);
        
        // Get all the leaves Based on Year
        $leaves = $this->getLeaves();
        if ($leaves->isEmpty()) {
        } else {
            // $leaveArray = collect();
            foreach ($leaves as $leave) {
                $leaveType = $leave->leave_type;
                $leaveCode = DB::table('leave_types')->where('id',$leaveType)->first();
                if($leaveCode){
                    $leaveCode = $leaveCode;
                }else{
                    $leaveCode = '';
                }
                $from = Carbon::parse($leave->datefrom);
                $to = Carbon::parse($leave->dateto);
                $diff = $from->diffInDays($to);
                $numOfFrom =  intval(Carbon::parse($leave->datefrom)->format('d'));
                $numOfTo =  intval(Carbon::parse($leave->dateto)->format('d'));
                $monthFromWord = Carbon::parse($leave->datefrom)->format('F Y');
                $monthToWord = Carbon::parse($leave->dateto)->format('F Y');
                if ($monthFromWord == $monthToWord) {
                    // If equal month means Leave consumed within a month
                    // $cellNumber = $this->monthToNumber($monthFromWord);
                    $convertedMonthFromWordToF = Carbon::parse($leave->datefrom)->format('F');
                    $cellNumber = $this->monthToNumber($convertedMonthFromWordToF);
                    for ($i = 0; $i <=  $diff; $i++) {
                        if (($i + $numOfFrom) < 25) {
                            $cellLetter = $this->valueToLetter($i + $numOfFrom);
                        } else {
                            // $cellLetter = $this->valueToLetter2(28);
                            $cellLetter = $this->valueToLetter2($i + $numOfFrom);
                        }
                        $sheet->setCellValue($cellLetter.''.$cellNumber,$leaveCode->code);
                    }
                } else {
                    $convertedMonthFromWordToF = Carbon::parse($leave->datefrom)->format('F');
                    $cellNumber = $this->monthToNumber($convertedMonthFromWordToF);
                    // First Loop for 1st month 
                    $maxDays = $this->getMaxDaysOfMonth($monthFromWord,$this->year);
                    for ($i = 0; $i <= 31; $i++) {
                        if (($i + $numOfFrom) < 25) {
                            $cellLetter = $this->valueToLetter($i + $numOfFrom);
                        } else {
                            $cellLetter = $this->valueToLetter2($i + $numOfFrom);
                        }
                        
                        $sheet->setCellValue($cellLetter.''.$cellNumber,$leaveCode->code);
                        if ($i + $numOfFrom >= $maxDays) {
                            break;
                        }
                    }
                    
                    $columnRange = array_merge(range('C', 'Z'), ['AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG']);
                    $datefrom = Carbon::createFromFormat('Y-m-d H:i:s', $leave->datefrom);
                    $datefrom2 = Carbon::createFromFormat('Y-m-d H:i:s', $leave->datefrom);
                    $datefrom2 = $datefrom2->addMonth()->format('F Y');

                    $dateto = Carbon::createFromFormat('Y-m-d H:i:s', $leave->dateto);
                    $dateto2 = $dateto->format('F Y');
                    $countMonths = $dateto->diffInMonths($datefrom);
                    if ($datefrom->day > $dateto->day) {
                        $countMonths++;
                    }

                    if ($countMonths < 1) {
                        $countMonths = 1;
                    }
                    for ( $i=0; $i < $countMonths;$i++){
                        
                        $maxDays = $datefrom->daysInMonth;
                        if($datefrom2 == $dateto2){
                            $timestamp = $leave->dateto; // 2023-07-02 00:00:00
                            $date = intval(Carbon::createFromFormat('Y-m-d H:i:s', $timestamp)->format('d'));
                            $maxDays = $date;
                        }
                        for($x=0;$x < $maxDays;$x++){
                            $convertedToF = DateTime::createFromFormat('F Y', $datefrom2)->format('F');
                            $monthNumber = $this->monthToNumber($convertedToF);
                            $xAdd = $x +1;
                            if ($xAdd < 25) {
                                $cellLetter = $this->valueToLetter($xAdd);
                            } else {
                                $cellLetter = $this->valueToLetter2($xAdd);
                            }
                            $sheet->setCellValue($cellLetter.''.$monthNumber,$leaveCode->code);

                        } 
                        $datefrom2 = Carbon::parse($datefrom2)->addMonth()->format('F Y'); // adding 1 month
                        
                    }

                    // For Earned Leave Vacation/Sick / Total
                    $monthToday = Carbon::now()->format('F');
                    $maxRange = $this->monthRange($monthToday);
                    for($i=0;$i < $maxRange;$i++){
                        $monthI = $this->getMonthName($i);
                        $convNum = $this->monthToNumber($monthI);
                        $sheet->setCellValue('AJ'.$convNum,'1.25');
                        $sheet->setCellValue('AK'.$convNum,'1.25');
                    }
                }
            }
        }
        $b=13;
        for($i=0;$i < 12;$i++){
            $sheet->setCellValue('AJ'.($i + $b),'1.25');
            $sheet->setCellValue('AK'.($i + $b),'1.25');
        }
    }
    function getMonthName($monthNumber) {
        $months = [
            0 => 'January', 1 => 'February', 2 => 'March', 3 => 'April', 4 => 'May', 5 => 'June',
            6 => 'July', 7 => 'August', 8 => 'September', 9 => 'October', 10 => 'November', 11 => 'December'
        ];

        return $months[$monthNumber] ?? ''; // Return the month name or an empty string if not found
    }


    function valueToLetter($value)
    {
        if ($value < 1) {
            return ""; // Invalid value
        } elseif ($value <= 26) {
            return chr($value + ord('C') - 1);
        } elseif ($value <= 702) {
            $tens = floor(($value - 1) / 26);
            $units = ($value - 1) % 26;
            return chr($tens + ord('A') - 3) . chr($units + ord('A') + 6);
        } else {
            return ""; // Invalid value
        }
    }
    function valueToLetter2($value)
    {
        $letters = ['AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG'];
        
        if ($value >= 25 && $value <= 31) {
            return $letters[$value - 25];
        } else {
            return ""; // Invalid value
        }
    }
    function aToZ($value)
    {
        $letters = range('A', 'Z');

        if ($value >= 1 && $value <= 26) {
            return $letters[$value - 1];
        } else {
            return ""; // Invalid value
        }
    }
    public function monthRange($month){
        $month = strtolower($month); // Convert to lowercase to handle case-insensitivity

        $monthMapping = [
            'january' => 0,
            'february' => 1,
            'march' => 2,
            'april' => 3,
            'may' => 4,
            'june' => 5,
            'july' => 6,
            'august' => 7,
            'september' => 8,
            'october' => 9,
            'november' => 10,
            'december' => 11,
        ];

        if($month < 0 || $month > 12){
            return '';
        }

        return $monthMapping[$month];
    }
    function monthToNumber($month)
    {
        $month = strtolower($month); // Convert to lowercase to handle case-insensitivity

        $monthMapping = [
            'january' => 13,
            'february' => 14,
            'march' => 15,
            'april' => 16,
            'may' => 17,
            'june' => 18,
            'july' => 19,
            'august' => 20,
            'september' => 21,
            'october' => 22,
            'november' => 23,
            'december' => 24,
        ];

        if (isset($monthMapping[$month])) {
            return $monthMapping[$month];
        } else {
            return 0; // Invalid month
        }
    }
    function getMaxDaysOfMonth($month, $year)
    {
        $month = ucfirst(strtolower($month)); // Convert to title case for month name

        $dateString = $year . '-' . Carbon::parse($month)->month . '-01';
        $date = Carbon::parse($dateString);

        return $date->daysInMonth;
    }

    public function getLeaves()
    {
        $results = DB::table('leaves')
        ->where('employee_id',$this->id)
        ->whereYear('datefrom', $this->year)
        ->get();
        if($results){
            return $results;
        }else{
            return 0;
        }
    }

    public function getDaysInMonth($year, $month)
    {
        // Create a Carbon instance for the given year and month
        $date = Carbon::createFromDate($year, $month, 1);

        // Get the number of days in the month
        $daysInMonth = $date->daysInMonth;

        return $daysInMonth;
    }

    public function query(){
        
        $employee = collect(DB::table('employees as e')
        // ->join('leaves as l','e.id','=','l.employee_id')
        ->leftJoin('designations as d','e.designation_id','=','d.id')
        ->where('e.id',$this->id)
        ->first());

        
        return $employee;

    }

    

    
}
