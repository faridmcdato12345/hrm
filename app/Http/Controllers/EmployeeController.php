<?php

namespace App\Http\Controllers;

use DB;
use Mail;
use Session;
use App\Branch;
use App\Employee;
use App\LeaveType;
use Carbon\Carbon;
use App\Department;
use App\Designation;
use App\EmployeeMemo;
use App\EmployeePds;
use App\EmployeeReceivables;
use App\Exports\PayrollExport;
use App\Exports\EmployeeList;
use App\Imports\CsvImport;
use App\Traits\EmployeeSocialNumberTrait;
use App\Traits\ZohoTrait;
use App\Traits\AsanaTrait;
use App\Traits\SlackTrait;
use Illuminate\Http\Request;
use App\Mail\ZohoInvitationMail;
use App\PointSheet;
use App\SocialWelfare;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Dompdf\Dompdf;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class EmployeeController extends Controller
{
    use AsanaTrait;
    use ZohoTrait;
    use SlackTrait;
    use EmployeeSocialNumberTrait;

    public $designations = [
        'ceo'                         => 'CEO',
        'project_coordinator'         => 'Project Coordinator',
        'web_developer'               => 'Web Developer',
        'junior_web_developer'        => 'Junior Web Developer',
        'front_end_developer'         => 'Front-end Developer',
        'account_sales_executive'     => 'Account Sales Executive',
        'sales_officer'               => 'Sales Officer',
        'digital_marketing_executive' => 'Digital Marketing Executive',
        'content_writer'              => 'Content Writer',
        'digital_marketer'            => 'Digital Marketer',
        'web_designer_lead'           => 'Web Designer Lead',
        'junior_web_designer'         => 'Junior Web Designer',
        'hr_manager'                  => 'HR Manager',
        'hr_officer'                  => 'HR Officer',
        'admin'                       => 'Admin',
    ];

    public $employment_statuses = [
        'permanent'   => 'Permanent',
        'contractual' => 'Contractual',
        'probation'   => 'Probation',
        'intern'      => 'Intern',
        'resigned'    => 'Resigned',
        'terminated'  => 'Terminated',
        'on_leave'    => 'On Leave',
    ];
    public $filters = [
        'all'         => 'all',
        'contractual' => 'contractual',
        'intern'      => 'intern',
        'on_leave'    => 'on_Leave',
        'permanent'   => 'permanent',
        'probation'   => 'probation',
        'resigned'    => 'resigned',
        'terminated'  => 'terminated',
    ];

    public function __construct()
    {
        // $this->middleware(['role_or_permission:super-admin|edit articles']);
    }

    public function index($id = '')
    {
        if ($id == 'all') {
            $data = Employee::with('branch', 'department')->get();
        } elseif ($id == '') {
            $data = Employee::with('branch', 'department')->where('status', '!=', '0')->get();
        } else {
            $data = Employee::with('branch', 'department')
                ->where('employment_status', $id)
                ->get();
        }
        $active_employees = Employee::where('status', '1')->count();
        $socialWelfares = SocialWelfare::all();
        return view('admin.employees.index', ['title' => 'All Employees'])
            ->with('employees', $data)
            ->with('active_employees', $active_employees)
            ->with('designations', Designation::all())
            ->with('filters', $this->filters)
            ->with('selectedFilter', $id)
            ->with('socialWelfares',$socialWelfares);
    }

    public function showDetail($id)
    {
        $employees = Employee::findOrFail($id);
        $memos = EmployeeMemo::where('employee_id', $id)->get();
        $receivables = EmployeeReceivables::where('employee_id',$id)->where('clear',0)->get();
        $pds = EmployeePds::where('employee_id',$id)->get();
        $departments = DB::connection('pgsql_external')->table('personnel_department')->where('dept_code',$employees->department_id)->get();
        return view('admin.employees.showDetail', 
        compact('memos','receivables','pds'),
        ['title' => $employees->firstname .' '.$employees->lastname])
        ->with('designations', Designation::all())
        ->with('employees', $employees)
        ->with('departments',$departments);
    }

    public function create()
    {
        return view('admin.employees.create', ['title' => 'Add Employee'])
            ->with('branches', Branch::all())
            ->with('departments', DB::connection('pgsql_external')->table('personnel_department')->get())
            ->with('employment_statuses', $this->employment_statuses)
            ->with('designations', Designation::all())
            ->with('social_welfares',SocialWelfare::all());
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'firstname'      => 'required',
            'lastname'       => 'required',
        ]);
        $token = config('values.SlackToken');
        $when = Carbon::now()->addMinutes(10);
        $l = 8;
        $password = bcrypt('lasureco');
		$query = "CAST(emp_code AS INT) DESC";
		$empCode = DB::connection('pgsql_external')->table('personnel_employee')->select('emp_code')->get();
		$empCodeFinal = $empCode->max('emp_code');
		$lastId = DB::connection('pgsql_external')->table('personnel_employee')->select('id')->get();
		$lastIdFinal = $lastId->max('id');
		$employeeId = (int)$lastIdFinal + 1;
		$employeeCode = (int)$empCodeFinal + 1;
        $arr = [
            'firstname'                      => $request->firstname,
            'lastname'                       => $request->lastname,
            'contact_no'                     => $request->contact_no,
            'emergency_contact'              => $request->emergency_contact,
            'emergency_contact_relationship' => $request->emergency_contact_relationship,
            'password'                       => $password,
            'status'                         => 1,
            'employment_status'              => $request->employment_status,
            'basic_salary'                   => $request->salary,
            'department_id'                  => $request->department_id,
            'designation_id'                 => $request->designation,
            'date_of_birth'                  => $request->date_of_birth,
            'current_address'                => $request->current_address,
            'permanent_address'              => $request->permanent_address,
            'city'                           => $request->city,
            'joining_date'                   => $request->joining_date,
            'gender'                         => strtolower($request->gender),
            'sss'                            => $request->sss_number,
            'pag_ibig'                       => $request->pagibig_number,
            'philhealth'                     => $request->philhealth_number,
            'tin'                            => $request->tin_number,
			'emp_id'						 => $employeeId,
			'emp_code'						 => $employeeCode,
            'contact_person'                 => $request->contact_person,
            'married_to'                     => $request->married_to,
            'hiredas'                        => $request->hiredas,
            'educational_attain'             => $request->educational_attain,
            'email_address'                  => $request->email_address
        ];
        if (! empty($request->branch_id)) {
            $arr['branch_id'] = $request->branch_id;
        }
        if ($request->picture != '') {
            $picture = time().'_'.$request->picture->getClientOriginalName();
            $request->picture->move('storage/employees/profile/', $picture);
            $arr['picture'] = 'storage/employees/profile/'.$picture;
        }
        $employee = Employee::create($arr);
        $latestEmployee = DB::table('employees')->latest()->first();
       
        $gender = '';
        if($latestEmployee->gender === 'Male'){
            $gender = 'M';
        }
        else{
            $gender = 'F';
        }
        $employeePgsql = DB::connection('pgsql_external')->insert('insert into personnel_employee (id,first_name,last_name,gender,status,emp_code,is_admin,enable_payroll,deleted,is_active,create_time) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [$employeeId, $request->firstname, $request->lastname, $gender , 0, $employeeCode, 0, true,false,true,Carbon::now()]);
        $params = [
            'emailAddress'        => $request->official_email,
            'primaryEmailAddress' => $request->official_email,
            'displayName'         => $request->firstname.' '.$request->lastname,
            'password'            => $password,
            'userExist'           => false,
            'country'             => 'pk',
        ];
        $employee_id = $employee->id;
        return redirect()->route('employees')->with('success', 'Employee is created succesfully');
    }
    public function storeEmployeeTimings($employee)
    {
        $employee = [
            'employee_id'  => $employee->employee_id,
            'timing_start' => $employee->branch->timing_start,
            'timing_off'   => $employee->branch->timing_start,
            'day'          => 'Monday',
        ];
        $employee = Employee::create($arr);
    }
    public function edit($id)
    {
        $employee = Employee::find($id);
        if (! $employee) {
            abort(404);
        }

        $employee_role_id = ''; //todo
        if ($employee->roles->count() > 0) {
            $employee_role_id = $employee->roles[0]->id; //todo
        }

        $employee_permissions = [];
        foreach ($employee->permissions as $key => $value) {
            $employee_permissions[] = $value->id;
        }

        $role = Role::find($id);
        $permissions = [];
        if ($role) {
            $permissions = $role->permissions()->get();
        }

        return view('admin.employees.edit', ['title' => 'Update Employee'])
            ->with('employee', $employee)
            ->with('branches', Branch::all())
            ->with('designations', Designation::all())
            ->with('departments', DB::connection('pgsql_external')->table('personnel_department')->get())
            ->with('employment_statuses', $this->employment_statuses)
            ->with('employee_role_id', $employee_role_id)
            ->with('permissions', $permissions)
            ->with('employee_permissions', $employee_permissions)
            ->with('roles', Role::all())
            ->with('social_welfares', SocialWelfare::all());
    }

    public function profile()
    {
        $employee = Auth::user();
        if (! $employee) {
            abort(404);
        }
        $employee_role_id = ''; //todo
        if ($employee->roles->count() > 0) {
            $employee_role_id = $employee->roles[0]->id; //todo
        }
        $employee_permissions = [];
        foreach ($employee->permissions as $key => $value) {
            $employee_permissions[] = $value->id;
        }
        $role = Role::find($employee->id);
        $permissions = [];
        if ($role) {
            $permissions = $role->permissions()->get();
        }
        return view('admin.employees.edit', ['title' => 'Update Employee'])
            ->with('employee', $employee)
            ->with('branches', Branch::all())
            ->with('designations', $this->designations)
            ->with('employment_statuses', Designation::all())
            ->with('employee_role_id', $employee_role_id)
            ->with('permissions', $permissions)
            ->with('employee_permissions', $employee_permissions)
            ->with('roles', Role::all());
    }
    public function update(Request $request, $id)
    {
        $adminPassword = Auth::user()->password;

        if (! Hash::check($request->old_password, $adminPassword)) {
            return redirect()->back()->with('error', 'Wrong admin password entered');
        }

        $this->validate($request, [
            'firstname'      => 'required',
            'lastname'       => 'required',
        ]);
        $employee = Employee::find($id);
        $employee->firstname = $request->firstname;
        $employee->lastname = $request->lastname;
        $employee->contact_no = $request->contact_no;
        if ($request->picture != '') {
            $picture = time().'_'.$request->picture->getClientOriginalName();
            $request->picture->move('storage/employees/profile/', $picture);
            $employee->picture = 'storage/employees/profile/'.$picture;
        }
        $employee->username = $request->username;
        $employee->joining_date = $request->joining_date;
        $employee->exit_date = $request->exit_date;
        $employee->emergency_contact = $request->emergency_contact;
        $employee->emergency_contact_relationship = $request->emergency_contact_relationship;
        $employee->basic_salary = $request->salary;
        $employee->designation_id = $request->designation_id;
        $employee->employment_status = $request->employment_status;
        if (! empty($request->branch_id)) {
            $employee->branch_id = $request->branch_id;
        }
        $employee->date_of_birth = $request->date_of_birth;
        $employee->current_address = $request->current_address;
        $employee->permanent_address = $request->permanent_address;
        $employee->city = $request->city;
        $employee->department_id = $request->department_id;
        $employee->gender = $request->gender;
        $employee->status = $request->status;
        $employee->contact_person = $request->contact_person;
        $employee->married_to = $request->married_to;
        $employee->hiredas = $request->hiredas;
        $employee->educational_attain = $request->educational_attain;
        $employee->email_address = $request->email_address;
        $employee->sss = $request->sss_number;
        $employee->philhealth = $request->philhealth_number;
        $employee->pag_ibig = $request->pagibig_number;
        if (! empty($request->password)) {
            $employee->password = Hash::make($request->password);
        }
        $when = Carbon::now()->addMinutes(10);
        if ($employee->roles->count() > 0) {
            $old_role = $employee->roles[0];
            $employee->removeRole($old_role);
        }
        if (!empty($request->role_id)) {
            $role = Role::find($request->role_id);
            $employee->assignRole($role);
        }
        if ($request->permissions) {
            foreach ($request->permissions as $permission_id) {
                if (isset($request->permissions_checked)) {
                    if (in_array($permission_id, $request->permissions_checked)) {
                        $employee->givePermissionTo($permission_id);
                    } else {
                        $employee->revokePermissionTo($permission_id);
                    }
                }
            }
        }
        $employee->save();
        // activity()->causedBy(Auth::user()->firstname)->log('edited');
        return redirect()->route('employees')->with('success', 'Employee is updated succesfully');
    }
    public function trashed()
    {
        $employee = Employee::onlyTrashed()->get();
        return view('admin.employees.trashed',
            ['title' => 'Trash Employees']
        )->with('employees', $employee);
    }
    public function kill($id)
    {
        $employee = Employee::withTrashed()->where('id', $id)->first();
        $employee->forceDelete();
        return redirect()->back()->with('success', 'Employee is deleted succesfully');
    }
    public function restore($id)
    {
        $employee = Employee::withTrashed()->where('id', $id)->first();
        $employee->restore();
        return redirect()->route('employees')->with('success', 'Employee is deleted succesfully');
    }
    public function destroy(Request $request, $id)
    {
        $this->validate($request, [
            'password' => 'required',
        ]);
        $adminPassword = Auth::user()->password;
        if (! Hash::check($request->password, $adminPassword)) {
            return redirect()->back()->with('error', 'Wrong admin password entered');
        }
        $emp = Employee::find($id);
        $account_id = $emp->account_id;
        $zuid = $emp->zuid;
        $email = $emp->official_email;
        $response = $emp->delete();
        if ($request->invite_to_zoho == 1) {
            $arr = [
                'zuid'     => $zuid,
                'password' => bcrypt($request->zoho_password), /*get pass from admin model box*/
            ];

            $this->deleteZohoAccount($arr, $account_id);
        }
        if ($request->invite_to_asana == 1) {
            $arr = [
                'zuid'     => $zuid,
                'password' => $adminPassword, /*get pass from admin model box*/
            ];
            $this->removeUser($email);
        }
        if ($request->invite_to_slack == 1) {
            //run bot
        }
        return redirect()->back()->with('success', 'Employee is trash succesfully');
    }
    public function EmployeeLogin()
    {
        return view('admin.employees.login');
    }
    public function postEmployeeLogin(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required',
            'password' => 'required',
        ]);

        $email = $request->email;
        $employee = Employee::where(['official_email' => $email])->first();
        if (isset($employee->password)) {
            return redirect()->back()->with('error', 'Email not found');
        }
        if (! Hash::check($request->password, $employee->password)) {
            return redirect()->back()->with('error', 'Wrong email/password entered');
        }

        if (isset($employee->id)) {
            $request->session()->put('emp_auth', $employee->id);

            return redirect()->route('employee.profile');
        }

        $messages = 'Username/Password Incorrect';

        return redirect()->back()->with('msg', $messages);
    }
    public function EmployeeProfile(Request $request)
    {
        $employee = Employee::find($request->session()->get('emp_auth'));
        return view('admin.employees.profile', ['employee' => $employee, 'title' => 'Update Profile']);
    }
    public function UpdateEmployeeProfile(Request $request, $id)
    {
        $this->validate($request, [
            'firstname' => 'required',
            'lastname'  => 'required',
        ]);
        $employee = Employee::find($id);
        $employee->firstname = $request->firstname;
        $employee->lastname = $request->lastname;
        $employee->contact = $request->contact;
        $employee->password = $request->password;
        $employee->emergency_contact = $request->emergency_contact;
        $employee->save();
        return redirect()->route('employees')->with('success', 'Employee is updated succesfully');
    }
    public function EmployeeLogout(Request $request)
    {
        $request->session()->forget('emp_auth');

        return redirect()->route('employee.login');
    }
    public function showDocs(Request $request)
    {
        $data = DB::table('employees')->where('id', $request->session()->get('emp_auth'))->get();
        $data2 = DB::table('uploads')->where('status', '=', 1)->get();
        return view('admin.employees.showDocs', ['data' => $data, 'files' => $data2, 'title' => 'All Documents']);
    }
    public function showAttendance(Request $request)
    {
        $this->meta['title'] = 'Show Attendance';
        $data = DB::table('employees')->where('id', $request->session()->get('emp_auth'))->get();
        $attendance = DB::table('attandances')->where('employee_id', $request->session()->get('emp_auth'))->get();
        $leave = DB::table('leaves')->where('employee_id', $request->session()->get('emp_auth'))->get();
        $events = [];

        if ($data->count()) {
            foreach ($attendance as $key => $value) {
                $events[] = Calendar::event(
                    'present',
                    true,
                    new \DateTime($value->checkintime),
                    new \DateTime($value->checkouttime.' +1 day'),
                    null,
                    [
                        'color' => 'green',
                    ]
                );
            }
            foreach ($leave as $key => $value) {
                $events[] = Calendar::event(
                    $value->leave_type,
                    true,
                    new \DateTime($value->datefrom),
                    new \DateTime($value->dateto.' +1 day'),
                    null,
                    [
                        'color' => 'orange',
                    ]
                );
            }
        }
        $calendar = Calendar::addEvents($events);
        return view('admin.employees.showAttendance', $this->metaResponse(), ['data' => $data, 'calendar' => $calendar]);
    }

    public function seedSlackId()
    {
        $token = config('values.SlackToken');
        $output = file_get_contents('https://slack.com/api/users.list?token='.$token.'&pretty=1');
        $output = json_decode($output, true);
        foreach ($output['members'] as $key => $member) {
            $employee = Employee::where('official_email', $member['profile']['email'])->first();
            if (isset($employee->id)) {
                $employee = Employee::where('official_email', $member['profile']['email'])->first();
                $employee->slack_id = $member['id'];
                $employee->save();
            } else {
                $employee = Employee::create([
                    'slack_id'       => $member['id'],
                    'official_email' => $member['profile']['email'],
                    'firstname'      => $member['profile']['first_name'],
                    'lastname'       => $member['profile']['last_name'],
                    'contact_no'     => $member['profile']['phone'],
                    'password'       => bcrypt('123456'),
                ]);
            }
        }
    }
    /**
     * method for importing data from excell
     */
    public function importDataFromExcel(){
        $reader = ReaderEntityFactory::createReaderFromFile(storage_path('Summary.xlsx'));
        $reader->open($filePath);
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $cells = $row->getCells();
            }
        }        
        $reader->close();
    }
    /**
     * method for pointing what sheet is to used for attendance
     */
    public function pointSheet(Request $request){
        $pointSheet = PointSheet::create($request->all());
    }
    /**
     * Employee Memo file 
     */
    public function storeEmployeeMemo(Request $request){
        $this->validate($request, [
            'document'      => 'required',
            'document_name' => 'required',
        ]);
        $arr = [];
        $arr = [
            'employee_id' => $request->employee_id,
            'file_name' => $request->document_name,
        ];
        if ($request->document != '') {
            $document = time().'_'.$request->document->getClientOriginalName();
            $request->document->move('storage/memo/', $document);
            $arr['url'] = 'storage/memo/'.$document;
            $arr['created_at'] = Carbon::now();
            $arr['updated_at'] = Carbon::now();
        }
        EmployeeMemo::insert($arr);
        Session::flash('success', 'File is uploaded successfully');
        return redirect()->route('employee.showDetail',['id'=>$request->employee_id]);
    }
    public function addPgsqlStore(Request $request){
        $lastRow = DB::connection('pgsql_external')->select('select id from personnel_employee order by create_time desc limit 1');
        
        $incrementId = intval($lastRow[0]->id) + 1;
        $employee = DB::connection('pgsql_external')->insert('insert into personnel_employee (id, first_name,last_name,gender,nickname,status,emp_code,is_admin,enable_payroll,deleted,is_active,create_time) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [4, $request->first_name, $request->last_name, $request->gender, $request->nickname, 0, 4, 0, true,false,true,Carbon::now()]);
        return json_encode($employee);
    }

    public function regularEmployees(){
        $employees = Employee::where('employment_status','permanent')->get();
        return view('admin.reports.leave.index',compact('employees'));
    }
    public function payrollPrint(Request $request){
        $filenameExport = "export.xlsx";
        return Excel::download(new PayrollExport($request->designation_id,$request->datefrom,$request->dateto), $filenameExport); 
    }
    public function listPrint(Request $request){
        $filenameExport = "list.xlsx";
        return Excel::download(new EmployeeList($request->designation_id), $filenameExport); 
    }
    
}
