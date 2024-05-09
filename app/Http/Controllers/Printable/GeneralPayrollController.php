<?php

namespace App\Http\Controllers\Printable;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Loan;
use App\LoanType;

class GeneralPayrollController extends Controller
{
    public function index(){
        return view('admin.printable.payroll');
    }
    public function generateTable(){
        $loans = Loan::all();
        $loanTypes = LoanType::all();
        
    }
}
