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


class PayrollExport implements WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public $depID;
    public $from;
    public $to;

    public function __construct($depID,$from,$to)
    {
        
        $this->department_id = $depID;
        $this->date_from = $from;
        $this->date_to = $to;

        $carbonDate = Carbon::parse($this->date_from);

        // Extract the year and month
        $this->year = $carbonDate->format('Y');
        $this->month = $carbonDate->format('m');
    }
    
    public function registerEvents(): array
    {
        return [
            BeforeWriting::class => function(BeforeWriting $event) {
                $templateFile = new LocalTemporaryFile(storage_path('import/IT_PAYROLL.xlsx'));
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
        $i = 6;
        $employeesList = $this->query($this->department_id);
        foreach ($employeesList as $item) {
            // Deduction minutes - if Leave
            $tardy = $this->getTardiness($item->emp_id,$item->id,$this->date_from,$this->date_to);
            $finalTardy = $this->finalcalc($tardy,$item->basic_salary,$this->month ,$this->year );
            
            // dd($item->designation_name);
            $sheet->insertNewRowBefore($i);
            $sheet->setCellValue('A'.$i,$i - 5);
            $sheet->setCellValue('B'.$i, ucfirst($item->firstname).' '.ucfirst($item->lastname));
            $sheet->setCellValue('C'.$i, $item->designation_name);
            $sheet->setCellValue('D'.$i, $item->basic_salary);
            $sheet->setCellValue('J'.$i, '=D'.$i.'/2');
            $sheet->setCellValue('S'.$i, $finalTardy);
            $sheet->setCellValue('U'.$i, '=SUM(K'.$i.':T'.$i.')');
            $sheet->setCellValue('V'.$i, '=SUM(J'.$i.'-U'.$i.')');
            $sheet->setCellValue('X'.$i,$i - 5);
            $i++;
        }
    }
    public function query($depID){
        
        $employee = DB::table('employees as e')
        ->leftJoin('designations as d','e.designation_id','=','d.id')
        ->select('e.firstname','e.lastname','e.basic_salary','d.designation_name','e.emp_id','e.id')
        ->where('department_id',$depID)
        ->where('e.status',1)
        ->orderBy('designation_id')
        ->get();
        return $employee;

    }

    public function getTardiness($id,$id2,$from,$to){
        $workingDays = $this->getWorkingDays($from,$to);
        // dd($workingDays);
        $count = count($workingDays);
        $totalTardy = 0;
        for($i=0;$i < $count;$i++){
            $day = $workingDays[$i]; 
            $clockIn = $this->getIn($day,$id);
            $clockOut = $this->getOut($day,$id);
            if($clockIn->isEmpty()){
                $parse = Carbon::parse($day);
                $parse1 = $parse->format('Y-m-d H:i:s');

                $leave = DB::table('leaves')
                ->where('employee_id', $id2)
                ->where(function ($query) use ($parse1) {
                    $query->whereDate('datefrom', '<=', $parse1)
                        ->whereDate('dateto', '>=', $parse1);
                })
                ->get();

                if($leave->isEmpty()){
                    $totalTardy += 240;
                }else{
                    $totalTardy += 0;
                }
            }else{

                $clockIn1 = $this->getIn($day,$id);
                // Format clockin to H:i:s
                $date = \Carbon\Carbon::createFromFormat("Y-m-d H:i:sO", $clockIn1['clock_in']);
                $formattedDate = $date->format('H:i:s');
                $totalTardy += $this->getTardyInMinutes($formattedDate,'am');
            }
            
            if($clockOut->isEmpty()){
                $parse = Carbon::parse($day);
                $parse1 = $parse->format('Y-m-d H:i:s');

                $leave = DB::table('leaves')
                ->where('employee_id', $id2)
                ->where(function ($query) use ($parse1) {
                    $query->whereDate('datefrom', '<=', $parse1)
                        ->whereDate('dateto', '>=', $parse1);
                })
                ->get();

                if($leave->isEmpty()){
                    $totalTardy += 240;
                }else{
                    $totalTardy += 0;
                }
            }else{
                $clockout1 = $this->getout($day,$id);
                // Format clockin to H:i:s
                if($clockout1['clock_out'] == NULL){
                    $parse = Carbon::parse($day);
                    $parse1 = $parse->format('Y-m-d H:i:s');

                    $leave = DB::table('leaves')
                    ->where('employee_id', $id2)
                    ->where(function ($query) use ($parse1) {
                        $query->whereDate('datefrom', '<=', $parse1)
                            ->whereDate('dateto', '>=', $parse1);
                    })
                    ->get();

                    if($leave->isEmpty()){
                        $totalTardy += 240;
                    }else{
                        $totalTardy += 0;
                    }
                }else{
                    $date = \Carbon\Carbon::createFromFormat("Y-m-d H:i:sO", $clockout1['clock_out']);
                    $formattedDate = $date->format('H:i:s');
                    $totalTardy += $this->getTardyInMinutes($formattedDate,'pm');
                }

                
            }
            
            

        }

        return $totalTardy;
    }

    public function getTardyInMinutes($time,$ampm){
        if($ampm == 'am'){
            $margin = "08:00:00";
            if ($time < $margin) {
                $time = $margin;
            }

            $carbon1 = Carbon::createFromFormat('H:i:s', $margin);
            $carbon2 = Carbon::createFromFormat('H:i:s', $time);
            $minutesDifference = $carbon1->diffInMinutes($carbon2);

            return $minutesDifference;
        }else{
            $margin = "16:00:00";
            if ($time > $margin) {
                $time = $margin;
            }

            $carbon1 = Carbon::createFromFormat('H:i:s', $margin);
            $carbon2 = Carbon::createFromFormat('H:i:s', $time);
            $minutesDifference = $carbon1->diffInMinutes($carbon2);
            
            return $minutesDifference;
        }
        
    }

    function getWorkingDays($startDate, $endDate){
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $workingDays = [];

        while ($start->lte($end)) {
            if ($start->isWeekday()) {
                $workingDays[] = $start->toDateString();
            }

            $start->addDay();
        }
        return $workingDays;
    }

    public function getIn($from,$id){
        $date_from = Carbon::parse($from);
        // $date_to = Carbon::parse($to);
        // dd($date_from,$date_to);
        $clockIn = collect(DB::connection('pgsql_external')->table('att_payloadtimecard')
                ->select('emp_id','clock_in','att_date')
                ->whereDate('att_date',$date_from)
                ->where('emp_id',$id)
                ->first());
        
            return $clockIn;
    }
    public function getOut($from,$id){
        $from = Carbon::parse($from);
        // $date_to = Carbon::parse($to);
        // dd($date_from,$date_to);
        $clockOut = collect(DB::connection('pgsql_external')->table('att_payloadtimecard')
                ->select('emp_id','clock_out','att_date')
                ->whereDate('att_date',$from)
                ->where('emp_id',$id)
                ->first());
        
        return $clockOut;
    }
    public function finalcalc($mins,$salary,$month,$year){

        $workingdays = $this->getWorkingDaysInMonth($month,$year);
        // $splitHalfWD = $workingdays /2;
        // $newSalary = $salary /2;
        // $minuteSalary = (($newSalary / $splitHalfWD) / 8) /60;

        // $finalDeduction = round($minuteSalary * $mins,2);
        $workingMinutes = $workingdays * 8 * 60;  // May 2023 -> 23 Working days
        $salaryPerMinute = $salary / $workingMinutes; 
        $finalDeduction = round($mins * $salaryPerMinute,2);

        return $finalDeduction;

    }
    function getWorkingDaysInMonth($month, $year)
    {
        // Create a Carbon instance for the first day of the month
        $startDate = Carbon::createFromDate($year, $month, 1);

        // Create a Carbon instance for the last day of the month
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // Create a CarbonPeriod object to iterate over the dates within the range
        $period = CarbonPeriod::create($startDate, $endDate);

        // Filter the dates to include only weekdays (Monday to Friday)
        $workingDays = $period->filter(function ($date) {
            return $date->isWeekday();
        });

        // Count the number of working days
        $workingDaysCount = $workingDays->count();

        return $workingDaysCount;
    }

    
}
