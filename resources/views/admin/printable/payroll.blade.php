@extends('layouts.printable')
@section('content')
<div>
    <div class="text-center"><h3 style="text-decoration: underline">GENERAL PAYROLL</h3></div>
    <div><p>We herby acknowledge to have received from AIMEE D. TOMAWIS, the sum herein stated or specified opposite our respective names & compensation for our services for the period <span class="payroll_date"></span></p></div>
    <div class="department">
        @php
            $e = DB::table('departments')->select('department_name')->where('id',$employees[0]->department_id)->value('department_name');
        @endphp
        <h2>{{$e}}</h2>
    </div>
</div>
<div>
    <table class="table table-bordered text-center" style="font-size: 12px;">
        <thead>
            <tr>
                <th scope="col">No.</th>
                <th scope="col">Name</th>
                <th scope="col">Position</th>
                <th scope="col">Basic Pay</th>
                @foreach($loans as $loan)
                    <th scope="col" class="th-{{$loan->id}}">{{$loan->name}}<br>Ers</th>
                @endforeach
                <th scope="col">Gross Pay</th>
                @php
                    $x = count($loanms) + 3;
                @endphp
                <th scope="colgroup" colspan="{{$x}}">
                    DEDUCTIONS
                </th>
                <th>NET PAY</th>
            </tr>
            <tr>
                <th scope="col"></th>
                <th scope="col"></th>
                <th scope="col"></th>
                <th scope="col"></th>
                @foreach($loans as $loan)
                    <th scope="col"></th>
                @endforeach
                <th scope="col"></th>
                @foreach($loanms as $l)
                <th scope="col">{{$l->socialWelfares->name}}<br>{{$l->name}}</th>
                @endforeach
                <th>Absent/<br>Late</th>
                <th>Cash<br>Advance</th>
                <th>Total<br>Deduction</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @php
                $x = 1;   
            @endphp
            @forelse ($employees as $data)
                <tr>
                    <td>{{$x++}}</td>
                    <td>{{$data->firstname}} {{$data->lastname}}</td>
                    <td>{{$data->designation}}</td>
                    <td>{{$data->basic_salary}}</td>
                    {{-- @foreach ($loans as $loan)
                        @if($data->loan_id == $loan->id)
                            <td class="th-{{$loan->id}}">{{$data->amount}}</td>
                        @else
                            <td class="th-{{$loan->id}}">NULL</td>
                        @endif
                    @endforeach --}}
                    <td></td>
                    <td></td>
                    <td>{{$data->basic_salary / 2}}</td>
                    <td></td>
                </tr>
            @empty
            <h1>No Data</h1>   
            @endforelse
        </tbody>
    </table>
</div>
@endsection

