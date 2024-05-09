<?php

namespace App\Http\Controllers;

use App\EmployeePds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;

class EmployeePdsAttachedController extends Controller
{
    public function store(Request $request){
        $validation = $request->validate([
			'employee_id' => 'required',
            'document_name' => 'nullable',
            'document' => 'nullable'
		]);
        $arr = [
            'employee_id' => $request->employee_id,
            'document' => $request->document,
            'document_name' => $request->document_name
        ];
        if ($request->document != '') {
            $document = time().'_'.$request->document->getClientOriginalName();
            $path = 'storage/employees/201';
            if(!Storage::exists($path)){
                Storage::makeDirectory($path, 0777, true, true);
            }   
            $request->document->move('storage/employees/201/', $document);
            $arr['document'] = 'storage/employees/201/'.$document;
        }
		EmployeePds::create($arr);
        Session::flash('success', 'Employee 201 file is saved');
        return redirect()->route('employee.showDetail',['id'=>$request->employee_id]);
    }
    public function showFile($id){
        $pds = EmployeePds::where('id',$id)->get();
        $filename = $pds[0]->document;
        $path = public_path($filename);
        return Response::make(file_get_contents($path), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline;filename="'.$filename.'"'
        ]);
    }
}
