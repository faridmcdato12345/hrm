<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\EmployeesImport;
use Maatwebsite\Excel\Facades\Excel;
use DB;


class EmployeeImportController extends Controller
{

    public function index(){
        return view('admin.employees.import');
    }
    public function store(Request $request){
        $collection = Excel::toCollection(new EmployeesImport, $request->file('document'));
        dd($collection);
    }
}
