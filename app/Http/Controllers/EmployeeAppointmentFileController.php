<?php

namespace App\Http\Controllers;

use App\Appointment;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use PDF;

class EmployeeAppointmentFileController extends Controller
{
    public function index($id){
        $appointment = Appointment::where('id',$id)->get();
        $filename = $appointment[0]->document;
        // dd($filename);
        $path = public_path($filename);
        return Response::make(file_get_contents($path), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline;filename="'.$filename.'"'
        ]);
    }
    public function export_pdf($id)
    {
        // Fetch all customers from database
        $appointment1 = DB::table('employees as e')
        ->join('designation_sg as sg','e.id','=','sg.emp_id')
        ->where('e.id',$id)->get();

        $employee =  DB::table('employees as e')->where('e.id',$id)->first();
        $appointment = $appointment1->map(function ($item){
            $designation = DB::table('designations as d')
            ->where('id',$item->designation_id)
            ->first();
            $date = date_create($item->created_at);
            return[
                'name'=> ucfirst($item->firstname).' '.ucfirst($item->lastname),
                'position'=> $designation->designation_name,
                'salary'=> 'Php '.number_format($item->salary,2),
                'status'=> 'IDK',
                'inclusive_dates'=> date_format($date,"M d, Y")
            ];
        });
        // dd($appointment);
        // Send data to the view using loadView function of PDF facade
        
        $pdf = PDF::loadView('print.service_record', compact('appointment','employee'));
        // If you want to store the generated pdf to the server then you can use the store function
        // $pdf->save(storage_path('/service_record').'dsdsasa.pdf');
        // Finally, you can download the file using download function
        return $pdf->download($employee->firstname.' '.$employee->lastname.' '.Carbon::now().'.pdf');
    }

}
