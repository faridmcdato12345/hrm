<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Applicant;
use Carbon\Carbon;
use App\Employee;
use Carbon\CarbonPeriod;
use App\AttendanceSummary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Spatie\Activitylog\Models\Activity;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::any('/register', function () {
    abort(403);
});

Route::get('/error', function () {
    return view('error');
})->name('error');

Route::get('/job/skill/{jobId}', [
    'uses' => 'JobsController@getSkillsByJob',
    'as'   => 'job.skill',
]);

Route::get('/home', 'HomeController@index')->name('home');

Route::post('/slackbot', 'AttendanceController@newSlackbot')->name('slackbot');

//Route::Post('/newSlackbot', 'AttendanceController@newSlackbot')->name('newSlackbot');

Route::group(['middleware' => 'auth'], function () {
	Route::resource('employee/appointment','EmployeeAppointmentController',['only' => ['index', 'create', 'store']]);
    Route::get('employee/appointment/{employee}','EmployeeAppointmentController@show')->name('appointment.show');
    Route::get('employee/appointment/detail/{employee}','EmployeeAppointmentDetailController@show')->name('appointment.detail.show');
    Route::get('employee/appointment/file/{id}','EmployeeAppointmentFileController@index')->name('employee.appointment.file');
	Route::get('employee/201/file/{id}','EmployeePdsAttachedController@showFile')->name('employee.pds.show');
	Route::post('employee/201/file','EmployeePdsAttachedController@store')->name('employee.pds.store');
    Route::get('/employee/appointment/service_record/{id}','EmployeeAppointmentFileController@export_pdf')->name('service.record.print');
    Route::post('/employee/payroll/print','EmployeeController@payrollPrint')->name('gen.payroll.print');
    Route::get('/employee/export/leave/form','EmployeeLeaveController@exportLeaveForm')->name('export.leave.form');
    Route::get('/employee/export/leave/{id}','EmployeeLeaveController@exportLeaveFile')->name('export.leave.file');
    Route::post('/employee/export/earnedLeave','EmployeeLeaveController@exportEarnedLeave')->name('export.leave.earned');
    Route::get('/employee/list/details','EmployeeController@listPrint')->name('employee.List.print');
    Route::get('/employee/list', function () {
        return view('admin.employees.employeeList');
    })->name('employee.list');
    Route::get('/employee/payroll', function () {
        return view('admin.employees.payroll');
    })->name('employee.payroll');
    Route::get('/holidays/{year}', 'HolidaysController@holidaysYearSearch')->name('holidays.search');
    Route::post('/holidays', 'HolidaysController@fetchNationHolidays')->name('holidays.fetch');
    Route::post('/holidays/update', 'HolidaysController@update')->name('holidays.update');
    Route::post('/holidays/add', 'HolidaysController@store')->name('holidays.store');
    Route::post('/holidays/delete', 'HolidaysController@delete')->name('holidays.delete');
    Route::get('/holidays', 'HolidaysController@index')->name('holidays');
    Route::get('/attendance/individual', 'IndividualAttendanceController@index')->name('individual.attendance');
    Route::post('/attendance/individual/show', 'IndividualAttendanceController@showAttendance')->name('individual.attendance.show');
    Route::post('/attendance/individual/edit', 'IndividualAttendanceController@edit')->name('individual.attendance.edit');
    Route::post('/attendance/individual/delete', 'IndividualAttendanceController@delete')->name('individual.attendance.delete');
    
    Route::group(['middleware' => ['can:AttendanceController:todayTimeline']], function () {
        Route::Get('/attendance/today_timeline/{id?}', [
            'uses' => 'AttendanceController@todayTimeline', //show Attendance
            'as'   => 'today_timeline',
        ]);
        Route::Post('/attendance/storeAttendanceSummaryToday', [
            'uses' => 'AttendanceController@storeAttendanceSummaryToday',
            'as'   => 'attendance.storeAttendanceSummaryToday',
        ]);
    });
	Route::patch('/timein/{id}','TimeInController@update');
		Route::resource('/timein','TimeInController');
		Route::patch('/timeout/{id}','TimeOutController@update');
		Route::resource('/timeout','TimeOutController');
		 Route::Get('/employees/{id?}', [
            'uses' => 'EmployeeController@index',
            'as'   => 'employees',
        ]);
        Route::Get('/all_employees', [
            'uses' => 'EmployeeController@all_employees',
            'as'   => 'all_employees',
        ]);
        Route::Get('/employee/create', [
            'uses' => 'EmployeeController@create',
            'as'   => 'employee.create',
        ]);
        Route::Post('/employee/store', [
            'uses' => 'EmployeeController@store',
            'as'   => 'employee.store',
        ]);
        //edit
        Route::get('/employee/edit/{id}', [
            'uses' => 'EmployeeController@edit',
            'as'   => 'employee.edit',
        ]);
		//update
        Route::Post('/employee/update/{id}', [
            'uses' => 'EmployeeController@update',
            'as'   => 'employee.update',
        ]);

        //trash
        Route::Get('/employee/trashed', [
            'uses' => 'EmployeeController@trashed',
            'as'   => 'employee.trashed',
        ]);

        Route::Get('/employee/kill/{id}', [
            'uses' => 'EmployeeController@kill',
            'as'   => 'employee.kill',
        ]);
        Route::Get('/employee/restore/{id}', [
            'uses' => 'EmployeeController@restore',
            'as'   => 'employee.restore',
        ]);
        Route::get('/list/receivables', [
            'uses' => 'EmployeeReceivablesController@index',
            'as'   => 'list.receivables',
        ]);

        //Delete Employee
        Route::Post('/employee/delete/{id}', [
            'uses' => 'EmployeeController@destroy',
            'as'   => 'employee.destroy',
        ]);
    Route::group(['middleware' => 'allowed_permission'], function () {
        //dashboard
		
        Route::get('/dashboard', [
            'uses' => 'DashboardController@index',
            'as'   => 'admin.dashboard',
        ]);

        Route::resources([
            'branch' => 'BranchesController',
        ]);

        Route::resources([
            'job' => 'JobsController',
        ]);

        Route::Get('/applicant/create', [
            'uses' => 'ApplicantController@create',
            'as'   => 'applicant.create',
        ]);
        Route::Get('/applicant', [
            'uses' => 'ApplicantController@index',
            'as'   => 'applicants',
        ]);
        Route::Get('/applicant/single_Cat_Job/{id}', [
            'uses' => 'ApplicantController@single_Cat_Job',
            'as'   => 'single_cat_jobs',
        ]);
        Route::Get('/applicant/single/{id}', [
            'uses' => 'ApplicantController@singleApplicant',
            'as'   => 'applicant.single',
        ]);
        Route::Get('/applicant/delete/{id}', [
            'uses' => 'ApplicantController@destroy',
            'as'   => 'applicant.delete',
        ]);
        Route::Get('/applicant/trashed', [
            'uses' => 'ApplicantController@trashed',
            'as'   => 'applicant.trashed',
        ]);
        Route::Get('/applicant/kill/{id}', [
            'uses' => 'ApplicantController@kill',
            'as'   => 'applicant.kill',
        ]);
        Route::Get('/applicant/restore/{id}', [
            'uses' => 'ApplicantController@restore',
            'as'   => 'applicant.restore',
        ]);
        Route::Get('/applicant/hire/{id}', [
            'uses' => 'ApplicantController@hire',
            'as'   => 'applicant.hire',
        ]);
        Route::Get('/applicant/retire/{id}', [
            'uses' => 'ApplicantController@retire',
            'as'   => 'applicant.retire',
        ]);
        Route::Get('/applicants/hired', [
            'uses' => 'ApplicantController@hiredApplicants',
            'as'   => 'applicants.hired',
        ]);
        //Department
        Route::Get('/departments', [
            'uses' => 'DepartmentController@index',
            'as'   => 'departments.index',
        ]);
        Route::post('/department/create', [
            'uses' => 'DepartmentController@create',
            'as'   => 'department.create',
        ]);
        Route::post('/department/update/{id}', [
            'uses' => 'DepartmentController@update',
            'as'   => 'department.update',
        ]);

        Route::post('/department/delete/{id}', [
            'uses' => 'DepartmentController@delete',
            'as'   => 'department.delete',
        ]);
        //Vendors
        Route::Get('/vendors', [
            'uses' => 'VendorController@index',
            'as'   => 'vendors.index',
        ]);
        Route::get('/vendor/create', [
            'uses' => 'VendorController@create',
            'as'   => 'vendor.create',
        ]);
        Route::post('/vendor/store', [
            'uses' => 'VendorController@store',
            'as'   => 'vendor.store',
        ]);
        Route::get('/vendor/edit/{id}', [
            'uses' => 'VendorController@edit',
            'as'   => 'vendor.edit',
        ]);
        Route::post('/vendor/update/{id}', [
            'uses' => 'VendorController@update',
            'as'   => 'vendor.update',
        ]);
        Route::post('/vendor/delete/{id}', [
            'uses' => 'VendorController@delete',
            'as'   => 'vendor.delete',
        ]);
        //Vendor Category
        Route::Get('/vendors/category', [
            'uses' => 'VendorCategoryController@index',
            'as'   => 'vendor_category.index',
        ]);
        Route::post('/vendor/category/create', [
            'uses' => 'VendorCategoryController@create',
            'as'   => 'vendor_category.create',
        ]);
        Route::post('/vendor/category/update/{id}', [
            'uses' => 'VendorCategoryController@update',
            'as'   => 'vendor_category.update',
        ]);

        Route::post('/vendor/category/delete/{id}', [
            'uses' => 'VendorCategoryController@delete',
            'as'   => 'vendor_category.delete',
        ]);

        //Teams
        Route::Get('/teams', [
            'uses' => 'TeamController@index',
            'as'   => 'teams.index',
        ]);
        Route::post('/team/create', [
            'uses' => 'TeamController@create',
            'as'   => 'team.create',
        ]);
        Route::post('/team/update/{id}', [
            'uses' => 'TeamController@update',
            'as'   => 'team.update',
        ]);

        Route::post('/team/delete/{id}', [
            'uses' => 'TeamController@delete',
            'as'   => 'team.delete',
        ]);

        //Team Members
        Route::post('/team_member/add/', [
            'uses' => 'TeamMembersController@create',
            'as'   => 'team_member.add',
        ]);
        Route::get('/team_member/edit/{id}', [
            'uses' => 'TeamMembersController@edit',
            'as'   => 'team_member.edit',
        ]);
        Route::post('/team_member/delete/{id}', [
            'uses' => 'TeamMembersController@delete',
            'as'   => 'team_member.delete',
        ]);

        
        //Profile Update
        Route::Get('/personal_profile/', [
            'uses' => 'ProfileController@index',
            'as'   => 'profile.index',
        ]);
        Route::Post('/profile/update', [
            'uses' => 'ProfileController@update',
            'as'   => 'password.update',
        ]);
        Route::Post('/profile_update/update', [
            'uses' => 'ProfileController@updatePic',
            'as'   => 'profile_pic.update',
        ]);

        //Leave Types

        Route::Get('/leave_types', [
            'uses' => 'LeaveTypeController@index',
            'as'   => 'leave_type.index',
        ]);
        Route::post('/leave_type/create', [
            'uses' => 'LeaveTypeController@create',
            'as'   => 'leave_type.create',
        ]);
        Route::post('/leave_type/update/{id}', [
            'uses' => 'LeaveTypeController@update',
            'as'   => 'leave_type.update',
        ]);

        Route::post('/leave_type/delete/{id}', [
            'uses' => 'LeaveTypeController@delete',
            'as'   => 'leave_type.delete',
        ]);
        //Skills
        Route::Get('/skills', [
            'uses' => 'SkillController@index',
            'as'   => 'skill.index',
        ]);
        Route::post('/skill/create', [
            'uses' => 'SkillController@create',
            'as'   => 'skill.create',
        ]);
        Route::post('/skill/update/{id}', [
            'uses' => 'SkillController@update',
            'as'   => 'skill.update',
        ]);

        Route::post('/skill/delete/{id}', [
            'uses' => 'SkillController@delete',
            'as'   => 'skill.delete',
        ]);

        //Sub Skills
        Route::Get('/Sub_skills', [
            'uses' => 'SubSkillController@index',
            'as'   => 'sub_skill.index',
        ]);
        Route::post('/sub_skill/add/', [
            'uses' => 'SubSkillController@create',
            'as'   => 'sub_skill.add',
        ]);
        Route::get('/sub_skill/edit/{id}', [
            'uses' => 'SubSkillController@edit',
            'as'   => 'sub_skill.edit',
        ]);
        Route::post('/sub_skill/sub_edit/{id}', [
            'uses' => 'SubSkillController@sub_edit',
            'as'   => 'sub_skill.sub_edit',
        ]);
        Route::post('/sub_skill/delete/{id}', [
            'uses' => 'SubSkillController@delete',
            'as'   => 'sub_skill.delete',
        ]);

        //Assign Skills To Employees
        Route::post('/assign_skill', [
            'uses' => 'SkillController@assign',
            'as'   => 'skill.assign',
        ]);
        Route::get('/assign_skill/edit/{id}', [
            'uses' => 'SkillController@assign_edit',
            'as'   => 'skill_assign.edit',
        ]);
        Route::post('/unassign_skill/employee/{id}', [
            'uses' => 'SkillController@unassign',
            'as'   => 'skill.unassign',
        ]);

        Route::resources([
            'organization_hierarchy' => 'OrganizationHierarchyController',
        ]);

        Route::Get('/rolespermissions', [
            'uses' => 'RolePermissionsController@index',
            'as'   => 'roles_permissions',
        ]);
        Route::Get('/rolespermissions/create', [
            'uses' => 'RolePermissionsController@create',
            'as'   => 'roles_permissions.create',
        ]);
        Route::Post('/rolespermissions/store', [
            'uses' => 'RolePermissionsController@store',
            'as'   => 'roles_permissions.store',
        ]);
        Route::Get('/rolespermissions/applyrole', [
            'uses' => 'RolePermissionsController@applyRole',
            'as'   => 'roles_permissions.applyrole',
        ]);
        Route::Post('/rolespermissions/applyrolepost', [
            'uses' => 'RolePermissionsController@applyRolePost',
            'as'   => 'roles_permissions.applyrolepost',
        ]);
        Route::Get('/rolespermissions/getPermissionsFromRole/{id}/{employee_id}', [
            'uses' => 'RolePermissionsController@getPermissionsFromRole',
            'as'   => 'roles_permissions.getPermissionsFromRole',
        ]);
        Route::Get('/rolespermissions/edit/{id}', [
            'uses' => 'RolePermissionsController@edit',
            'as'   => 'roles_permissions.edit',
        ]);
        Route::Post('/rolespermissions/update/{id}', [
            'uses' => 'RolePermissionsController@update',
            'as'   => 'roles_permissions.update',
        ]);

        Route::Post('/rolespermissions/delete/{id}', [
            'uses' => 'RolePermissionsController@destroy',
            'as'   => 'roles_permissions.delete',
        ]);

       

        Route::get('/profile', [
            'uses' => 'EmployeeController@profile',
            'as'   => 'employee.profile',
        ]);

        
        //show attendance import excel
        Route::get('/attendance/import',[
            'uses'=>'AttendanceController@showImportExcel',
            'as'=>'attendance.import',
        ]);
        //save attendance import excel
        Route::post('/attendance',[
            'uses'=>'AttendanceController@saveImportExcel',
            'as'=>'save.excel',
        ]);
        //attendance
        Route::Get('/attendance/show/{id?}', [
            'uses' => 'AttendanceController@showAttendance', //show Attendance
            'as'   => 'attendance',
        ]);
        //attendance
        Route::Get('/attendance/timeline/{id?}', [
            'uses' => 'AttendanceController@showTimeline', //show Attendance
            'as'   => 'timeline',
        ]);
        
       

        // Route::Resource('attendance','AttendanceController');

        Route::Get('/attendance/sheet/{id}', [
            'uses' => 'AttendanceController@sheet', //show Attendance sheet
            'as'   => 'attendance.sheet',
        ]);

        Route::Get('/attendance/create/{id?}/{date?}/', [
            'uses' => 'AttendanceController@create', //show Attendance
            'as'   => 'attendance.create',
        ]);

        Route::Get('/attendance/createByAjax/{id?}/{date?}/', [
            'uses' => 'AttendanceController@createByAjax', //show Attendance
            'as'   => 'attendance.createByAjax',
        ]);

        //Attendance and leave check for ajax for shown in update form
        Route::Get('/attendance/getbyAjax', [
            'uses' => 'AttendanceController@getbyAjax',
            'as'   => 'attendance.showByAjax',
        ]);

        Route::Get('/attendance/edit/{id}', [
            'uses' => 'AttendanceController@edit',
            'as'   => 'attendance.edit',
        ]);
        

        Route::Post('/attendance/store', [
            'uses' => 'AttendanceController@store',
            'as'   => 'attendance.store',
        ]);
        Route::Post('/attendance/store_break', [
            'uses' => 'AttendanceController@storeBreak',
            'as'   => 'attendance.storeBreak',
        ]);

        Route::Get('/attendance/show/{id}', [
            'uses' => 'AttendanceController@index',
            'as'   => 'attendance.show',
        ]);
        Route::Post('/attendance/delete', [
            'uses' => 'AttendanceController@destroy',
            'as'   => 'attendance.destroy',
        ]);

        Route::Post('/attendance/deletechecktime', [
            'uses' => 'AttendanceController@deleteChecktime',
            'as'   => 'attendance.deletechecktime',
        ]);

        Route::Post('/attendance/update', [
            'uses' => 'AttendanceController@update',
            'as'   => 'attendance.update',
        ]);

        Route::GET('/attendance/export', [
            'uses' => 'AttendanceController@showExport',
            'as'   => 'attendance.export.show',
        ]);

        Route::Post('/attendance/export', [
            'uses' => 'AttendanceController@exportAttendance',
            'as'   => 'attendance.export',
        ]);
        //Attendance Break

        Route::Get('/attendance/create_break/{id?}/{date?}/', [
            'uses' => 'AttendanceController@createBreak', //show Attendance
            'as'   => 'attendance.createBreak',
        ]);

        Route::Post('/attendance/deletebreakchecktime', [
            'uses' => 'AttendanceController@deleteBreakChecktime',
            'as'   => 'attendance.deleteBreakChecktime',
        ]);

        Route::Post('/attendance/update_break', [
            'uses' => 'AttendanceController@updateBreak',
            'as'   => 'attendance.updateBreak',
        ]);

        //Salary Show

        Route::Get('/salary/{id?}', [
            'uses' => 'SalariesController@index',
            'as'   => 'salaries.show',
        ]);

        //add Bonus
        Route::Post('/salary/addBonus/{id}', [
            'uses' => 'SalariesController@addBonus',
            'as'   => 'salaries.bonus',
        ]);
        //proccessed

        Route::Post('/salary/process', [
            'uses' => 'SalariesController@processSalary',
            'as'   => 'salaries.processed',
        ]);

        //export
        Route::Get('/salary/export', [
            'uses' => 'SalariesController@index',
            'as'   => 'salaries.index',
        ]);

        Route::Get('/salary/export', [
            'uses' => 'SalariesController@index',
            'as'   => 'salaries.index',
        ]);

        Route::Post('/salary/export', [
            'uses' => 'SalariesController@export',
            'as'   => 'salaries.export',
        ]);

        Route::Get('/employee_leaves', [
            'uses' => 'LeaveController@employeeleaves',
            'as'   => 'employeeleaves',
        ]);
        Route::Get('/employee/leave/add/{id}', [
            'uses' => 'EmployeeLeaveController@create',
            'as'   => 'employeeaddleaves',
        ]);
        Route::post('/employee/leave/add/', [
            'uses' => 'EmployeeLeaveController@store',
            'as'   => 'employee.leave.store',
        ]);
        Route::get('/employee/leave/record/{id}', [
            'uses' => 'EmployeeLeaveController@show',
            'as'   => 'employee.leave.show',
        ]);
        Route::Get('/leave/edit/{id}', [
            'uses' => 'LeaveController@edit',
            'as'   => 'leave.edit',
        ]);

        Route::Get('/leave/show/{id}', [
            'uses' => 'LeaveController@show',
            'as'   => 'leave.show',
        ]);

        Route::Post('/leave/update/{id}', [
            'uses' => 'LeaveController@update',
            'as'   => 'leave.update',
        ]);

        Route::Get('/leave/updateStatus/{id}/{status}', [
            'uses' => 'LeaveController@updateStatus',
            'as'   => 'leave.updateStatus',
        ]);

        Route::Post('/leave/delete/{id}', [
            'uses' => 'LeaveController@destroy',
            'as'   => 'leave.destroy',
        ]);

        Route::Post('/leave/delete/{id}', [
            'uses' => 'LeaveController@leaveDelete',
            'as'   => 'leave.delete',
        ]);

        
    });
	//Designations
        Route::Get('/designations', [
            'uses' => 'DesignationController@index',
            'as'   => 'designations.index',
        ]);
        Route::post('/designation/create', [
            'uses' => 'DesignationController@create',
            'as'   => 'designation.create',
        ]);
        Route::post('/designation/update/{id}', [
            'uses' => 'DesignationController@update',
            'as'   => 'designation.update',
        ]);

        Route::post('/designation/delete/{id}', [
            'uses' => 'DesignationController@delete',
            'as'   => 'designation.delete',
        ]);
	//upload Docs
        Route::get('/documents', [
            'as'   => 'documents',
            'uses' => 'DocumentsController@index',
        ]);

        Route::get('/documents/create', [
            'as'   => 'documents.create',
            'uses' => 'DocumentsController@createDocs',
        ]);

        Route::post('/documents/upload', [
            'as'   => 'documents.upload',
            'uses' => 'DocumentsController@uploadDocs',
        ]);

        Route::post('/documents/delete/{id}', [
            'as'   => 'documents.delete',
            'uses' => 'DocumentsController@deleteDocument',
        ]);

        Route::get('/documents/edit/{id}', [
            'as'   => 'documents.edit',
            'uses' => 'DocumentsController@editDocument',
        ]);

        Route::post('/documents/update/{id}', [
            'as'   => 'documents.update',
            'uses' => 'DocumentsController@update',
        ]);
    //My Attendance
    Route::GET('/attendance/myAttendance/{id?}', [
        'uses' => 'AttendanceController@authUserTimeline',
        'as'   => 'myAttendance',
    ]);
    Route::post('/attendance/correction_email', [
        'uses' => 'AttendanceController@correctionEmail',
        'as'   => 'correction_email',
    ]);
    //	Help
    Route::get('/help', [
        'uses' => 'DashboardController@help',
        'as'   => 'admin.help',
    ]);
    Route::post('/contact_us', [
        'uses' => 'DashboardController@contact_us',
        'as'   => 'contact_us',
    ]);

    Route::get('/report/leave',[
        'uses' => 'EmployeeController@regularEmployees',
        'as'    => 'report.leave',
    ]);

    Route::post('/report/leave/output/{id}',[
        'uses' => 'LeaveController@leaveOutput',
        'as' => 'report.leave.output',
    ]);

    Route::post('/report/leave/output/ajax/{id}',[
        'uses' => 'LeaveController@leaveOuputAjax',
        'as'   => 'report.leave.output.ajax'
    ]);

    Route::post('/leave/get-earned-last-year/',[
        'uses' => 'TotalEarnedLeavesController@getEarnedLastYear',
        'as' => 'leave.get.earned.last.year'
    ]);
    Route::post('/store/total_earned/ajax/{id}',[
        'uses' => 'TotalEarnedLeavesController@storingAjax',
        'as' => 'store.total.earned'
    ]);
    Route::get('/leave/history/{id}',[
        'uses' => 'TotalEarnedLeavesController@history',
        'as' => 'leave.history'
    ]);
    Route::post('/leave/count/{id}',[
        'uses' => 'TotalEarnedLeavesController@checkingVlSlCount',
        'as' => 'check.leave.count'
    ]);
    Route::get('/employee/attendance',[
        'uses' => 'EmployeeAttendanceController@getEmployeeAttendanceUnderSupervisory',
        'as' => 'employee.attendance.supervisory'
    ]);
    Route::get('/employee/absent',[
        'uses' => 'EmployeeAbsentController@absentEmployee',
        'as' => 'employee.absent'
    ]);
    Route::get('/employee/attendance/manual',[
        'uses' => 'Generated\\ScheduledAttendanceController@manualIndex',
        'as' => 'manual.attendance'
    ]);
    Route::post('/employee/attendance/manual',[
        'uses' => 'Generated\\ScheduledAttendanceController@manualStoring',
        'as' => 'post.manual.attendance'
    ]);
    
    Route::post('employee/ajaxLeave/',[
        'uses' => 'EmployeeLeaveController@ajaxCheckEmployeeLeave',
        'as' => 'ajax.employee.leave'
    ]);
    Route::get('/loan/loan_category/','LoanTypeController@index')->name('loan.category.index');
    Route::post('/loan/loan_category/{id}','LoanTypeController@store')->name('loan.category.store');
    Route::post('/loan/loan_category/update/{id}',[
        'uses' => 'LoanTypeController@update',
        'as' => 'loan.category.update',
    ]);
    Route::post('/loan/loan_category/delete/{id}',[
        'uses' => 'LoanTypeController@destroy',
        'as' => 'loan.category.delete',
    ]);
    Route::get('/employee/get/loan/{id}',[
        'uses' => 'EmployeeLoanController@getLoan',
        'as' => 'employee.get.loan'
    ]);
    Route::post('/employee/loan/',[
        'uses' => 'EmployeeLoanController@postLoanCategory',
        'as' => 'employee.loan.category.post'
    ]);
    
    // Route::get('/employee/loan/{id}',[
    //     'uses' => 'EmployeeLoanController@showEmployeeLoans',
    //     'as' => 'employee.show.loan'
    // ]);
    Route::get('/cash_advance',[
        'uses' => 'CashAdvanceController@index',
        'as' => 'cash.advance.index'
    ]);
    Route::get('/cash_advance/employee',[
        'uses' => 'CashAdvanceController@getEmployee',
        'as' => 'cash.advance.employee'
    ]);
    Route::get('/cash_advance/create',[
        'uses' => 'CashAdvanceController@create',
        'as' => 'cash.advance.create'
    ]);
    Route::resource('employee/overtime', 'OvertimeController');
    Route::resource('overtime/formula','OvertimeFormulaController');
    Route::resource('/cooperative_share','CooperativeShareController');
    Route::resource('/loan_management','LoanManagementController');
    Route::resource('/social_welfares','SocialWelfareController');
    Route::resource('/membership','MembershipController');
    Route::resource('/loan','LoanController');
    Route::post('/employee/loan/add/{id}','EmployeeLoanController@addLoanToEmployee')->name('add.employee.loan');
    Route::get('/employee/membership/show/{id}','EmployeeMembershipController@showMembershipEmployee')->name('show.employee.membership');
    Route::get('/employee/membership/{id}','EmployeeMembershipController@createMembershipEmployee')->name('create.employee.membership');
    Route::post('/employee/membership/{id}','EmployeeMembershipController@addMembershipEmployee')->name('add.employee.membership');
    Route::get('/test/date',function(){
        $d = date('Y-m-d');
        $date = Carbon::parse($d);
        $lastDayofMonth = Carbon::now()->daysInMonth;
        dd(gettype($lastDayofMonth));
        //dd($date->addDays(14)->format('Y-m-d'));
        $datess = $date->toDateString();
        $cabonNow = Carbon::now()->format('Y-m-d');
        if(Carbon::now()->format('Y-m-d') >= Carbon::now()->firstOfMonth()->addDays(14)->format('Y-m-d') && Carbon::now()->format('Y-m-d') <= Carbon::now()->lastOfMonth()->format('Y-m-d')){
            dd('yes');
        }
        else{
            dd(Carbon::now()->daysInMonth);
        }
        // dd(Carbon::now()->firstOfMonth()->addDays(14)->format('Y-m-d'));
    });
    Route::post('/cash_advance/{id}',[
        'uses' => 'CashAdvanceController@store',
        'as' => 'cash.advance.store'
    ]);
    Route::get('/payroll',[
        'uses' => 'Printable\\GeneralPayrollController@index',
        'as' => 'employee.payroll.index'
    ]);
    Route::get('/payroll_payments',[
        'uses' => 'PayrollPaymentController@index',
        'as' => 'payroll.payment'
    ]);

    Route::post('/payroll_payments',[
        'uses' => 'PayrollPaymentController@generatePayroll',
        'as' => 'payroll.payment.generate'
    ]);
	
	Route::get('/employee/import','EmployeeImportController@index')->name('employee.import.index');
	Route::post('/employee/import','EmployeeImportController@store')->name('employee.import.store');

	Route::get('/unclaimed/salary/create','UnclaimedSalaryController@create')->name('salary.create');
	Route::get('/unclaimed/salary/{id?}','UnclaimedSalaryController@getEmployee')->name('salary.getEmployee');
	Route::post('/unclaimed/salary','UnclaimedSalaryController@store')->name('salary.store');
	Route::patch('/unclaimed/salary/{id}','UnclaimedSalaryController@update')->name('salary.update');

	Route::get('/unclaimed/thirteen','UnclaimedThirteenController@create')->name('thirteen.create');
	Route::get('/unclaimed/thirteen_month/{id?}','UnclaimedThirteenController@getEmployee')->name('thirteen.getEmployee');
	Route::post('/unclaimed/thirteen','UnclaimedThirteenController@store')->name('thirteen.store');
	Route::patch('/unclaimed/thirteen/{id}','UnclaimedThirteenController@update')->name('thirteen.update');

	Route::get('/unclaimed/benefit/create','UnclaimedBenefitController@create')->name('unclaim.benefit.create');
	Route::get('/unclaimed/benefit/{id?}','UnclaimedBenefitController@getEmployee')->name('unclaim_benefit.getEmployee');
	Route::post('/unclaimed/benefit','UnclaimedBenefitController@store')->name('unclaim.benefit.store');
	Route::patch('/unclaimed/benefit/{id}','UnclaimedBenefitController@update')->name('unclaim.benefit.update');

	Route::resource('/benefit','BenefitController');
	Route::post('/benefit/update/{id}','BenefitController@updates')->name('benefit.updates');
	Route::post('/benefit/delete/{id}','BenefitController@destroy')->name('benefit.destroys');

	Route::get('/employee/show_detail/{id}','EmployeeController@showDetail')->name('employee.showDetail');
	Route::post('/employee/store/memo/','EmployeeController@storeEmployeeMemo')->name('employee.store.memo');
	
	Route::get('/dtr','DtrController@index')->name('dtr.index');
	Route::post('/dtr','DtrController@newPrintDtr')->name('dtr.print');
});

Route::resource('/colleague_leave','LeaveLogController');

Route::Get('/my_leaves', [
    'uses' => 'LeaveController@index',
    'as'   => 'leave.index',
]);
//Leaves
Route::Get('/leave/create', [
    'uses' => 'LeaveController@create',
    'as'   => 'leaves',
]);

Route::Get('/leave/admin_create/{id?}', [
    'uses' => 'LeaveController@adminCreate',
    'as'   => 'admin.createLeave',
]);

Route::Post('/leave/store', [
    'uses' => 'LeaveController@store',
    'as'   => 'leaves.store',
]);
Route::Post('/leave/admin_store', [
    'uses' => 'LeaveController@adminStore',
    'as'   => 'leaves.adminStore',
]);

Route::Get('/applicant/apply', [
    'uses' => 'ApplicantController@create',
    'as'   => 'applicant.apply',
]);
Route::Post('/applicant/store', [
    'uses' => 'ApplicantController@store',
    'as'   => 'applicant.store',
]);
Route::get('/findjob', 'ApplicantController@findjob');

Route::Post('/attendance/delete/{id}', [
    'uses' => 'AttendanceController@Attendance_Summary_Delete',
    'as'   => 'attendance.delete',
]);
Route::get('/employee/leaves',function(){
    return view('admin.leaves.employeeleaves');
});
//Route::get('sendmail', 'SendMailController@sendMail');

//Route::get('/ajax-job',function(){
//		$cat_id = Input::get('cat_id');
//	$jobs = Jobs::where('job_position_id', '=',$cat_id)->get();
//	return Response::json($jobs);
// });

Route::any('/search', function () {
    $q = Input::get('q');
    $applicant = Applicant::where('city', 'LIKE', '%'.$q.'%')->get();
    if (count($applicant) > 0) {
        return view('searchview')->withDetails($applicant)->withQuery($q);
    } else {
        return view('searchview')->withMessage('No Details found. Try to search again !');
    }
});
// Route::get('welcome-mail','@welcomeMail');
Route::get('/import-excel','EmployeeController@importDataFromExcel');
Route::post('/excel-sheet-point',[
    'uses'=>'EmployeeController@pointSheet',
    'as'=>'pointSheet',
]);


Route::get('/employee/add/pgsql',function(){
    return view('admin.employees.pgsql.index');
});
Route::post('/employee/add/pgsql','EmployeeController@addPgsqlStore')->name('employee.add.pgsql.store');
Route::get('/scrapping',function(){
    return view('admin.scrapping.index');
});
// Route::get('/generate/attendance',function(){
//     $biometricAttance = DB::connection('pgsql_external')->select('select * from att_payloadtimecard');
//     dd($biometricAttance);
// });
Route::post('/employee/receivables/{id}','EmployeeReceivablesController@clear')->name('receivable.clear');
Route::resource('/employee/receivables', 'EmployeeReceivablesController');
Route::post('/employee/setting/total_earned_leaves/{id}','TotalEarnedLeavesController@stores')->name('total_earned_leaves.stores');
Route::resource('/employee/setting/total_earned_leaves','TotalEarnedLeavesController');

Route::get('/generate/attendance','Generated\\ScheduledAttendanceController@index');

Route::get('/user/activity',function(){
    return view('admin.activity_log.index');
})->name('user.activity');
// Route::get('/test/act',function(){
//     $a = Activity::all();
//     $b = $a->map(function($item,$key){
//         return [
//             'subject_id' => DB::table('employees')->where('id',$item['subject_id'])->value('firstname'),
//             'description' => $item['description'],
//             'causer_id' => DB::table('employees')->where('id',$item['causer_id'])->value('firstname'),
//             'created_at' => $item['created_at']->toDateString(),
//         ];
//     });
//     dd($b->all());
// });
// Route::get('/test/att',function(){
//     // $a = AttendanceSummary::with('employee.department')->get();
//     // $a = AttendanceSummary::with(["employee"=>function($query){
//     //     $query->where('employees.department_id','=',5);
//     // }])->get();
//     $a = AttendanceSummary::with("employee.department")->whereHas("employee", function($query){
//         $query->where('employees.department_id',3);
//     })->whereBetween('date',['2021-06-01','2021-06-31'])->get();
//     $b = $a->groupBy('employee_id');
//     dd($b);
//     // $a = AttendanceSummary::find(1);
//     // $b = $a->employee()->where('department_id','!=',3)->get();
//     // $startDate = Carbon::createFromFormat('Y-m-d', '2020-11-01');
//     // $endDate = Carbon::createFromFormat('Y-m-d', '2020-11-05');
//     // $dateRange = CarbonPeriod::create($startDate, $endDate);
//     // dd(collect($dateRange->toArray()));
// });

// Route::get('/test/pgsql',function(){
//     //$lastRow = DB::connection('pgsql_external')->select('select id,emp_code,first_name from personnel_employee order by create_time asc');
//     /*foreach($lastRow as $employee){
// 		DB::table('employees')->insert([
// 			'emp_id' => $employee->id,
// 			'emp_code' => $employee->emp_code,
// 			'firstname' =>  $employee->first_name,
// 		]);
// 	}*/
// 	$bioTimeAttendances = DB::connection('pgsql_external')->table('personnel_employee')->get();
// 	$today = date('Y-m-d');
// 	/*$bioTimeAttendances = Employee::with([
//                 'attendanceSummary' => function ($join) use ($today) {
//                     $join->where('date', $today);
//                 },
//             ], 'branch', 'leaves', 'designations')
// 			->first();*/
// 	//$bioTimeAttendances = DB::table('employees')
// 	//					->join(DB::connection('pgsql_external')->table('att_payloadtimecard')->get(),'employees.id', '=', 'att_payloadtimecard.id')
// 	//					->get();
// 	//$bioTimeAttendances = DB::connection('pgsql_external')->table('att_payloadtimecard')->orderByDesc('att_date')->get();
// 	/*$bioTimeAttendances = DB::connection('pgsql_external')->table('att_payloadtimecard')
// 						->join(DB::table('employees')->get(),'att_payloadtimecard.id','=','employees.id')
// 						->get();*/
//         $biotimeAttendance = [];
//         $totaltime = 0;
//         $datess = new stdClass();
//         /*foreach($bioTimeAttendances as $bioTimeAttendance){
//             // dd(gettype(json_encode(Carbon::parse($bioTimeAttendance->clock_out)->format('Y-m-d H:i:s'))));    
//             $e = DB::connection('pgsql_external')->table('personnel_employee')->select('id','emp_code')->where('id',$bioTimeAttendance->emp_id)->first();
// 			$employeeCode = $e->emp_code;
// 			$datess->in = Carbon::parse($bioTimeAttendance->clock_in)->format('Y-m-d H:i:s');
//             $datess->out = Carbon::parse($bioTimeAttendance->clock_out)->format('Y-m-d H:i:s');
//             $myDate = json_encode($datess);
//             $datess->in = Carbon::parse($datess->in);
//             $datess->out = Carbon::parse($datess->out);
//             $in = json_decode(Carbon::parse($bioTimeAttendance->clock_in)->format('Y-m-d H:i:s'));
//             $out = json_decode(Carbon::parse($bioTimeAttendance->clock_out)->format('Y-m-d H:i:s'));
//             $totaltime += $datess->out->diffInMinutes($datess->in);
//             DB::table('attendance_summaries')->insert([
// 				'employee_id' => $employeeCode,
//                 'first_timestamp_in' => $datess->in,
//                 'last_timestamp_out' => $datess->out,
//                 'total_time' => $totaltime,
//                 'date' => $bioTimeAttendance->att_date,
//                 'created_at' => Carbon::now(),
//                 'updated_at' => Carbon::now(),
//                 'status' => 'present',
//                 'is_delay' => 'no'
//             ]);
//         }*/
// 		/*$today = Carbon::now()->toDateString();
// 		$employees = Employee::with([
//                 'attendanceSummary' => function ($join) use ($today) {
//                     $join->where('date', $today);
//                 },
//             ],'leaves', 'designations')
//             ->where('status', '!=', '0')->first();*/
// 	dd($bioTimeAttendances);
// 	/*$x = DB::connection('pgsql_external')
// 		->table('att_payloadtimecard')
// 		->where('emp_id',3)
// 		->whereDate('att_date','2021-07-12')
// 		->first();
// 	dd($x->clock_in);*/
// });
// Route::get('/api/v1/get_attendance','Api\GetAttendanceController@index')->name('api.get.attendance');
Route::get('/test/department',function(){
	$d = DB::connection('pgsql_external')->table('personnel_department')->get();
	dd($d);
	
});
Route::get('/insert/employee',function(){
	$lastRow = DB::connection('pgsql_external')->select('select id,emp_code,first_name,last_name from personnel_employee order by create_time asc');
    foreach($lastRow as $employee){
		DB::table('employees')->insert([
			'emp_id' => $employee->id,
			'emp_code' => $employee->emp_code,
			'firstname' =>  $employee->first_name,
			'lastname' => $employee->last_name,
		]);
	}
});
// Route::get('/test/attendance',function(){
// 	$d = DB::connection('pgsql_external')->table('att_payloadtimecard')
//     ->where('emp_id',216)
//     ->whereDate('att_date','2022-11-02')
//     ->take(10)->get();
// 	dd($d);
// });
// Route::get('/test/attendance_employee',function(){
// 	/*$d = DB::connection('pgsql_external')->table('att_payloadtimecard')
// 	->whereBetween('att_date',['2021-07-12','2021-07-23'])
// 	->orderBy('att_date','ASC')->get();*/
// 	$emp = Employee::with('designations')->where('emp_code',416)->get();
//    // dd($emp);
// 	$e = collect([]);
// 	/*$e->push([
// 		'price'=>100,
// 		'name'=>'f',
// 	]);*/
// 	foreach($emp as $em){
// 		$d = DB::connection('pgsql_external')->table('att_payloadtimecard')
// 			->whereBetween('att_date',['2022-11-01','2022-11-08'])
// 			->where('emp_id','=',$em->emp_id)
// 			->orderBy('att_date','ASC')
//             ->take(10)
//             ->get();
//             dd($d);
            
// 			foreach($d as $s){
// 				$e->push([
// 					'emp_name' => $em->firstname .' '. $em->lastname,
// 					'emp_dept_id' => $em->department_id,
// 					'emp_code' => $em->emp_code,
// 					'emp_id' => $em->emp_id,
// 					'clock_in' => $s->clock_in,
// 					'clock_out' => $s->clock_out,
//                     'att_date' => $s->att_date
// 				]);
// 			}
// 	}
// 	dd($d);
// });
// Route::get('/test/date_year_month',function(){
//     $date = date('Y-m-d H:i:s');
//     $c = explode("-",$date);
//     dd($c);
// });
// Route::get('/employee/test',function(){
	
// 	$emp = Employee::with('designations')->where('id',217)->get();
//     dd($emp);
// });
// Route::get('/dtr/test',function(){
// 	$from = Carbon::parse('2021-09-01');
// 	$to = Carbon::parse('2021-09-15');
// 	$fromPass = Carbon::parse('2021-09-01')->format('m/d/Y');
// 	$toPass = Carbon::parse('2021-09-15')->format('m/d/Y');
// 	$x = $from->diff($to);
// 	$c = collect();
// 	$cc = collect();
// 	$employee = Employee::select('id','emp_id','emp_code','firstname','lastname','designation','basic_salary')->where('department_id',2)->orderBy('designation')->limit(5)->get();
// 	$department = DB::connection('pgsql_external')->table('personnel_department')->select('dept_name')->where('id',2)->first();
// 	$margin = DB::table('time_ins')->select('margin')->where('status',1)->pluck('margin');
// 	$margins = strtotime($margin[0]);
// 	$uu = date('H:i:s',$margins);
	
// 	foreach($employee as $key => $value){
// 		$from = Carbon::parse('2021-09-01');
// 		$total_absent = array();
// 		$totalTardi = array();
// 		for($i=0;$i<=$x->days;$i++){
// 			$clockIn = DB::connection('pgsql_external')->table('att_payloadtimecard')
// 						->select('emp_id','clock_in')
// 						->whereDate('att_date',Carbon::parse($from))
// 						->where('emp_id',$value->emp_id)
// 						->first();
// 			$clockOut = DB::connection('pgsql_external')->table('att_payloadtimecard')
// 						->select('emp_id','clock_out')
// 						->whereDate('att_date',Carbon::parse($from))
// 						->where('emp_id',$value->emp_id)
// 						->first();
			
// 			$ins = ($clockIn) ? get_object_vars($clockIn) : NULL;
// 			$outs = ($clockOut) ? get_object_vars($clockOut) : NULL;
// 			$in = ($ins) ? strtotime(Carbon::parse($ins['clock_in'])->format('H:i:s')) : NULL;
// 			if($in){
// 				if($in > $margins){
// 					$re = new DateTime(Carbon::parse($ins['clock_in'])->format('H:i:s'));
// 					$f = new DateTime($margin[0]);
// 					$late = $f->diff($re)->format("%H:%I:%S%");
// 				}
// 				else{
// 					$late = '00:00:00';
// 				}
// 			}else{
// 				$late = '00:00:00';
// 			}
// 			if($ins && $outs){
// 				$absent = '--';
// 			}else{
// 				$sat = "saturday";
// 				$sun = "sunday";
// 				$dt1 = strtotime($from);
// 				$dt2 = date("l", $dt1);
// 				$dt3 = strtolower($dt2);
// 				if(strcmp($dt3,$sat) === 0 || strcmp($dt3,$sun) === 0){
// 					$absent = "--";
// 				}
// 				else{
// 					$absent = 1;
// 					array_push($total_absent,'08:00:00');
// 				}
// 			}
// 			array_push($totalTardi,$late);
// 			$all_seconds = 0;
// 			foreach ($totalTardi as $time) {
// 				list($hour, $minute, $second) = explode(':', $time);
// 				$all_seconds += $hour * 3600;
// 				$all_seconds += $minute * 60; 
// 				$all_seconds += $second;

// 			}
// 			$total_minutes = floor($all_seconds/60); 
// 			$seconds = $all_seconds % 60; 
// 			$hours = floor($total_minutes / 60); 
// 			$minutes = $total_minutes % 60;
// 			$all_absent_seconds = 0;
// 			foreach ($total_absent as $time) {
// 				list($hour, $minute, $second) = explode(':', $time);
// 				$all_absent_seconds += $hour * 3600;
// 				$all_absent_seconds += $minute * 60; 
// 				$all_absent_seconds += $second;
// 			}
// 			$total_absent_minutes = floor($all_absent_seconds/60);
// 			$absent_seconds = $all_absent_seconds % 60; 
// 			$absent_hours = floor($total_absent_minutes / 60); 
// 			$absent_minutes = $total_absent_minutes % 60;
// 			$totalAmountAbsent = $absent_hours + $absent_minutes + $absent_seconds;
// 			$sz = Carbon::parse($outs['clock_out'])->format('H:i:s');
// 			$xx = strtotime($sz);
// 			$c->push([
// 				'emp_id' => $value->id,
// 				'emp_name' => $value->firstname.' '.$value->lastname,
// 				'date' => Carbon::parse($from)->format('Y-m-d'),
// 				'date_string' => Carbon::parse($from),
// 				'clock_in' => ($ins) ? Carbon::parse($ins['clock_in'])->format('g:i A') : '--',
// 				'clock_out' => ($outs && $xx >= 1632812400) ? Carbon::parse($outs['clock_out'])->format('g:i A') : '--',
// 				'late' => $late,
// 				'job' => $value->designation,
// 				'emp_code'=>$value->emp_code,
// 				'designation' => $value->designation,
// 				'absent' => $absent,
// 				'total_tardi' => sprintf('%02d:%02d:%02d', $hours, $minutes,$seconds),
// 				'total_absent' => sprintf('%02d:%02d:%02d',$absent_hours,$absent_minutes,$absent_seconds),
// 			]);
// 			$from->addDays(1);
// 		}
// 	}
// 	$attendances = $c->groupBy('emp_name');
// 	//dd($attendances);
// 	$atts = $c;
// 	return view('admin.dtr.print_new',compact('attendances','fromPass','toPass','department','diffs'));
// });
// Route::get('/test/time', function(){

//     $time1 = "10:30:00";
//     $time2 = "20:15:00";

//     $seconds1 = strtotime($time1);
//     $seconds2 = strtotime($time2);

//     $sum = $seconds1 + $seconds2;

//     $result = date("H:i:s", $sum);

//     echo $result;

// });
