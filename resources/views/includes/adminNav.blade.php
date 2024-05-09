<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar" style="overflow: hidden; width: auto; height: 100%;">
        <!-- User profile -->
        <div class="user-profile" style="background: url({{asset('assets/images/background/user-info.jpg') }}) no-repeat;">
            <!-- User profile image -->
            <div class="profile-img"> <img src="{{asset(Auth::user()->picture)}}" onerror="this.src ='{{asset('assets/images/default.png')}}';" alt="user" height="50" width="50%" /> </div>
            <!-- User profile text-->
            <div class="profile-text"> <a href="#" class="dropdown-toggle u-dropdown" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">{{Auth::user()->firstname}}</a>
            <div class="dropdown-menu animated flipInY"> <a href="{{route('profile.index')}}" class="dropdown-item"><i class="ti-user"></i> My Profile</a> <a href="#" class="dropdown-item"><i class="ti-email"></i> Inbox</a>
            <div class="dropdown-divider"></div> <a href="#" class="dropdown-item"><i class="ti-settings"></i> Account Setting</a>
            <div class="dropdown-divider"></div> <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="dropdown-item"><i class="fa fa-power-off"></i> Logout</a> </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
            </div>
        </div>
        <!-- End User profile text-->
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                {{--/////Second Start--}}
                @if (Auth::user()->isAllowed('DashboardController'))
                <li @if(request()->is('dashboard')) class="active" @endif ><a class="waves-effect waves-dark" href="{{route('admin.dashboard')}}" aria-expanded="false"><i class="mdi mdi-gauge"></i><span class="hide-menu">Dashboard </span></a>
                </li>
                @endif
                @can('EmployeeController:index') 
				<li @if( request()->is('employees')  || request()->is('teams') || request()->is('vendors') || request()->is('vendor/create')  || str_contains( Request::fullUrl(),'organization_hierarchy') || request()->is('employee/create') || str_contains(Request::fullUrl(),'employee/edit') || str_contains(Request::fullUrl(),'team_member')) class = "active" @endif > 
                    <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                        <i class="mdi mdi-account"></i>
                        <span class="hide-menu">Employee Mngmt</span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{route('employees')}}" @if(request()->is('employee/create') || str_contains(Request::fullUrl(),'employee/edit')) class="active" @endif>Employees</a></li>
                        <li><a href="{{route('appointment.index')}}" @if(str_contains(Request::fullUrl(),'employee/appointment')) class="active" @endif>Appointment</a></li>
                        <li><a href="{{route('employee.payroll')}}" @if(str_contains(Request::fullUrl(),'employee/payroll')) class="active" @endif>Payroll</a></li>
                        <li><a href="{{route('employee.list')}}" @if(str_contains(Request::fullUrl(),'employee/list')) class="active" @endif>Employee Details</a></li>
                        @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('Supervisor') || Auth::user()->hasRole('OGM'))
                        <li><a href="{{route('employeeleaves')}}">Leaves</a></li>
                        @endif
                        <li><a href="{{route('list.receivables')}}" @if(request()->is('list/receivables') || str_contains(Request::fullUrl(),'list/receivables')) class="active" @endif>Receivables</a></li>
                    </ul>
                </li>
                @endcan
                <li  @if(str_contains(Request::fullUrl(),'attendance') || request()->is('leave/create') || request()->is('leave/admin_create') || request()->is('my_leaves') || request()->is('employee_leaves') || str_contains(Request::fullUrl(),'leave/edit')|| str_contains(Request::fullUrl(),'leave/show')) class = "active" @endif ><a class="has-arrow waves-effect waves-dark" href="{{route('attendance')}}"><i class="mdi mdi-alarm-check"></i><span class="hide-menu">Attendance</span></a>
                    <ul aria-expanded="false" class="collapse">
                        @can('AttendanceController:todayTimeline')
                        <li><a href="{{route('today_timeline')}}" @if( str_contains(Request::fullUrl(),'attendance/create')) class="active" @endif>Today</a></li>
                        @endcan
                        @can('IndividualAttendanceController:index')
                        <li><a href="{{route('individual.attendance')}}" @if(str_contains(Request::fullUrl(),'attendance/individual')) class="active" @endif >Individual</a></li>
                        @endcan
                        <!-- <li><a href="{{route('myAttendance')}}">My Attendance</a></li> -->
                        <!-- @if (
                        Auth::user()->isAllowed('AttendanceController:timeline')
                        )
                        <li><a href="{{route('timeline')}}">Timeline</a></li>
                        @endif -->
                        <!-- @if ( Auth::user()->employment_status == 'permanent')
                        <li><a href="{{route('leave.index')}}" @if(request()->is('leave/create') || str_contains(Request::fullUrl(),'leave/edit') || str_contains(Request::fullUrl(),'leave/show')) class="active" @endif >My Leaves</a></li>
                        @endif -->
                    </ul>
                </li>
                @can('DocumentsController:index')
                <li  @if( str_contains(Request::fullUrl(),'documents') || str_contains(Request::fullUrl(),'branch') || str_contains(Request::fullUrl(),'department') || str_contains(Request::fullUrl(),'designations') || request()->is('leave_types')  || request()->is('skills') || request()->is('vendors/category') ) class="active" @endif><a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false"><i class="mdi mdi-settings"></i><span class="hide-menu">Settings</span></a>
                    <ul aria-expanded="false" class="collapse">
                        @can('HolidaysController:index')
                        <li><a href="{{route('holidays')}}" @if(str_contains(Request::fullUrl(),'holidays')) class="active" @endif >Holidays</a></li>
                        @endcan
                        @can('DocumentsController:index')
                        <li><a href="{{ route('documents') }}" @if(str_contains(Request::fullUrl(),'documents')) class="active" @endif >Documents</a></li>
                        @endcan
                        @can('DepartmentController:index')
                        <li><a href="{{route('departments.index')}}">Departments</a></li>
                        @endcan
                        @can('DesignationController:index')
                        <li><a href="{{route('designations.index')}}">Job Description</a></li>
                        @endcan
                        @can('LeaveController:index')
                        <li><a href="{{route('leave_type.index')}}">Leave Mngmt</a></li>
                        @endcan
                        @can('BenefitController:index')
						<li style=""><a href="{{route('benefit.index')}}">Benefit Mngmt</a></li>
						@endcan
						@can('LoanController:index')
                        <li style=""><a href="{{route('loan.index')}}">Loan Mngmt</a></li>
						@endcan
						@can('SocialWelfareController:index')
                        <li style=""><a href="{{route('social_welfares.index')}}">Social Welfare Mngmt</a></li>
						@endcan
						@can('MembershipController:index')
                        <li style=""><a href="{{route('membership.index')}}">Membership Mngmt</a></li>
						@endcan
						@can('CooperativeShareController:index')
                        <li style=""><a href="{{route('cooperative_share.index')}}">Coop Share Mngmt</a></li>
                        @endcan
                        @if(Auth::user()->hasRole('hr') || Auth::user()->hasRole('admin') || Auth::user()->hasRole('CEO'))
                        <li style=""><a href="{{route('total_earned_leaves.index')}}">Previous Total Earned Leaves</a></li>
                        @endif
						@can('TimeInController:index')
						<li style=""><a href="{{route('timein.index')}}">Time-in</a></li>
						@endcan
						@can('TimeOutController:index')
						<li style=""><a href="{{route('timeout.index')}}">Time-out</a></li>
						@endcan
                    </ul>
                </li>
                @endcan
                <!-- <li><a class="has-arrow" href="#" aria-expanded="false"><i class="mdi mdi-database"></i><span class="hide-menu">Payments</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{route('salaries.show')}}">Salary</a></li>
                        <li><a href="{{route('payroll.payment')}}">Payroll Payments</a></li>
                    </ul>
                </li> -->
                @if (
                    Auth::user()->isAllowed('RolePermissionsController:index')
                )
                <li @if(request()->is('rolespermissions/create') ||request()->is('rolespermissions')) class="active" @endif > <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false"><i class="mdi mdi-apps"></i><span class="hide-menu">Manage Roles</span></a>
                    <ul aria-expanded="false" class="collapse">
                        @if (
                        Auth::user()->isAllowed('RolePermissionsController:index')
                        )
                        <li><a href="{{route('roles_permissions')}}" @if(request()->is('rolespermissions/create')) class="active" @endif  >Roles And Permissions</a></li>
                        @endif
                    </ul>
                </li>
                @endif
                @if(Auth::user()->designation == 'admin')
                <li style=""><a href="{{route('cash.advance.index')}}" aria-expanded="false"><i class="mdi mdi-cash"></i><span class="hide-menu">Cash Advance</span></a></li>
                <li style=""><a class="has-arrow" href="#" aria-expanded="false"><i class="mdi mdi-checkbox-blank-circle"></i><span class="hide-menu">Formula</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('formula.index') }}">Overtime</a></li>
                        <li><a href="{{ route('thirteen.getEmployee') }}">Leave</a></li>
                    </ul>
                </li>
				@can('UnclaimedSalaryController')
                <li style=""><a class="has-arrow" href="#" aria-expanded="false"><i class="mdi mdi-checkbox-blank-circle"></i><span class="hide-menu">Unclaimed</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('salary.getEmployee') }}">Salary</a></li>
                        <li><a href="{{ route('thirteen.getEmployee') }}">13 Month Pay</a></li>
                        <li><a href="{{ route('unclaim_benefit.getEmployee') }}">Benefits</a></li>
                    </ul>
                </li>
				@endcan
                <li>
                    <a href="{{route('user.activity')}}" aria-expanded="false"><i class="mdi mdi-checkbox-blank-circle"></i><span class="hide-menu">Activity Log</span></a>
                </li>
                @endif
                @can('DtrController:index')
                <li>
                    <a href="{{route('dtr.index')}}" aria-expanded="false"><i class="mdi mdi-checkbox-blank-circle"></i><span class="hide-menu">DTR</span></a>
                </li>
                @endcan
                {{--///////// Second End--}}
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
    <!-- Bottom points-->
    <div class="sidebar-footer">
        <!-- item--><a href="" class="link" data-toggle="tooltip" title="Settings"><i class="ti-settings"></i></a>
        <!-- item--><a href="https://www.zoho.com/mail/index1.html" class="link" data-toggle="tooltip" title="Email"><i class="mdi mdi-gmail"></i></a>
        <!-- item--><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="link" data-toggle="tooltip" title="Logout"><i class="mdi mdi-power"></i></a></div>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
    <!-- End Bottom points-->
</aside>