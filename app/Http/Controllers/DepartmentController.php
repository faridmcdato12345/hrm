<?php

namespace App\Http\Controllers;

use Session;
use App\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = DB::connection('pgsql_external')->table('personnel_department')->get();

        return view('admin.departments.index')->with('departments', $departments);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'department_name' => 'required',
            'status'          => 'required',
        ]);
		$latestDepartment = DB::connection('pgsql_external')->table('personnel_department')->latest('id')->select('id')->first();
		$s = $latestDepartment->id + 1;
		$departmentPgsql = DB::connection('pgsql_external')->insert('insert into personnel_department (id, dept_code,dept_name,is_default,parent_dept_id) values (?, ?, ?, ?, ?)', [$s, $s, $request->department_name, true , null]);
		Session::flash('success', 'Department is created successfully');
        return redirect()->route('departments.index');
    }

    public function update(Request $request, $id)
    {
		$department = DB::connection('pgsql_external')->table('personnel_department')
						->where('id',$id)
						->update([
							'dept_name' => $request->department_name,
						]);
        Session::flash('success', 'Department is updated successfully');

        return redirect()->route('departments.index');
    }

    public function delete(Request $request, $id)
    {
		$department = DB::connection('pgsql_external')->table('personnel_department')->where('id',$id)->delete();
        Session::flash('success', 'Department deleted successfully.');
        return redirect()->route('departments.index');
    }
}
