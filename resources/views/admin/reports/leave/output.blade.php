<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Document</title>
    <link href="{{asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        .td-width{
            width: 30%;
            text-align: right;
        }
        @media print{
            @page {
                size: landscape;
                
            }
            *{
                font-size:11px;
            }
        }
    </style>
</head>
<body class="container-fluid" style="padding-top: 1%">
    <input type="hidden" value="{{$year}}" class="year">
    <h3 class="text-center">LANAO DEL SUR ELECTRIC COOPERATIVE, INC.</h3>
    @if($employee->count() > 0)
    <h4 class="text-center">{{$employee->employee->department->department_name}} Department</h4>
<div class="row">
   
    <div class="col-md-6">
        <p style="text-transform: capitalize;">Name of Employee : {{$employee->employee->firstname}} {{$employee->employee->lastname}}</p>
        <p>Designation: {{$employee->employee->designation}}</p>
        <p>Date of Employment: {{$employee->employee->joining_date}}</p>
    </div>
    <div class="col-md-6">
        <p style="text-transform: capitalize;">Employee ID Number : </p>
        <p>Nature of Employment: {{$employee->employee->employment_status}}</p>
        <p>YEAR: {{$year}}</p>
    </div>
    @else
    <p></p>
    @endif
</div>
<table class="table-bordered table">
    <thead>
        <tr>
            <th colspan="32" class="text-center">{{$year}}</th>
            <th colspan="2">REMARKS</th>
            <th colspan="2">EARNED LEAVE</th>
            <th>GRAND</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Month</td>
            @for($i=1;$i<=31;$i++)
                <td>{{$i}}</td>
            @endfor
            <td>VL</td>
            <td>SL</td>
            <td>Vacation</td>
            <td>Sick</td>
            <td><b>TOTAL</b></td>
        </tr>
        @php
            $months = ["january","february","march","april","may","june","july","august","september","october","november","december"];    
            $x = 0;
        @endphp
        @foreach ($months as $month)
            @php
                $x++;    
            @endphp
            <tr @if(strlen($x) == 1) class="{{sprintf("%02d",$x)}}" @else class="{{$x}}" @endif>
                <td style="text-transform: capitalize;">{{$month}}</td>
                @for($i=1;$i<=31;$i++)
                    <td @if (strlen($i) == 1) class="{{sprintf("%02d",$i)}}" @else class="{{$i}}" @endif></td>
                @endfor
                <td class="vl-total"></td>
                <td class="sl-total"></td>
                <td>1.25</td>
                <td>1.25</td>
                <td>2.50</td>
            </tr>
        @endforeach
    </tbody>
</table>
<div class="row">
    <div class="col-sm-8">
        <p>Code:</p>
        <p>Vacation Leave:_____________    VL</p>
        <p>Sick Leave:_________________    SL</p>
        <p>Tardiness:__________________    T</p>
        <p>Request for Absensces / Leave:__RA</p>
        <p>Time Card:__________________    TC</p>
        <p id="datefrom"></p>
    </div>
    <div class="col-sm-4">
        <table style="width:100%">
            <tr>
                <th></th>
                <th class="td-width">Vac. Leave</th>
                <th class="td-width">Sick Leave</th>
            </tr>
            <tr></tr>
            <tr>
                <td>Total Earned Last Year</td>
                <td class="total_last_year_vl td-width"></td>
                <td class="total_last_year_sl td-width"></td>
            </tr>
            <tr>
                <td>Add: Leave Earned This Year</td>
                <td class="this-year-vl td-width">15.00</td>
                <td class="this-year-sl td-width">15.00</td>
            </tr>
            <tr>
                <td>Total Earned This Year</td>
                <td class="total_this_year_vl td-width"></td>
                <td class="total_this_year_sl td-width"></td>
            </tr>
            <tr>
                <td>Less: RA Leave</td>
                <td class="leave_record_vl td-width"></td>
                <td class="leave_record_sl td-width"></td>
            </tr>
            <tr>
                <td>Balance to Next Year</td>
                <td class="balance_vl td-width"></td>
                <td class="balance_sl td-width"></td>
            </tr>
            <tr>
                <td>C.D.O</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Grand Total</td>
                <td class="grand_total td-width text-center" colspan="2"></td>
                <td class="td-widths"></td>
            </tr>
        </table>
    </div>
</div>
<div class="row pt-5">
    <div class="col-md-6"></div>
    <div class="col-md-6">
        <div class="row">
            <div class="col-md-6">
                <label for=""><b>Prepared by:</b></label>
                <p style="text-transform: capitalize;" class="text-center">{{Auth::user()->firstname}} {{Auth::user()->lastname}}</p>
                <hr>
                <p class="text-center">Record Officer</p>
            </div>
            <div class="col-md-6">
                <label for=""><b>Checked by:</b></label>
                <p style="text-transform: capitalize;" class="text-center">&nbsp;</p>
                <hr>
                <p class="text-center">HR. Sec. Head</p>
            </div>
        </div>
    </div>
</div>
</body>
@include('includes.adminScripts')
<script>
    $(document).ready(function(){
        let id = "{{$leaveId}}"
        let year = "{{$year}}"
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        getEearnedLastYear()
        let postUrl = "{{route('report.leave.output.ajax',':id')}}";
        postUrl = postUrl.replace(':id',id);
        $.ajax({
            data: {year:year},
            url: postUrl,
            type: "post",
            dataType: "json",
            success: function(data){
                let zAdd = 0;
                let yAdd = 0;
                for(var i = 0; i < Object.keys(data[0]).length; i++){
                    let datefrom = data[0][i]['datefrom']
                    let dateFromSplit = datefrom.split(" ")[0]
                    let dateFromSplits = datefrom.split("-")
                    let dateFromMonth = dateFromSplits[1]
                    let dateFromDay = dateFromSplits[2]
                    let d = dateFromDay.split(" ")[0]
                    let dateto = data[0][i]['dateto']
                    let dateToSplit = dateto.split(" ")[0]
                    let dateToSplits = dateto.split("-")
                    let dateToMonth = dateToSplits[1]
                    let dateToDay = dateToSplits[2]
                    let x = dateToDay.split(" ")[0]
                    let z = 0;
                    let y = 0;
                    for(var u = d; u<=x; u++){
                        if(data[0][i]['leave_type'] == 1){
                            $('tr.'+dateFromMonth+' td.'+u+'').text("SL")
                            z++
                            zAdd++
                            $('tr.'+dateFromMonth+' td.sl-total').text(z)
                        }
                        if(data[0][i]['leave_type'] == 2){
                            $('tr.'+dateFromMonth+' td.'+u+'').text("VL")
                            y++
                            yAdd++
                            $('tr.'+dateFromMonth+' td.vl-total').text(y)
                        }
                        
                    }
                }
                $('.leave_record_vl').text(yAdd)
                $('.leave_record_sl').text(zAdd)
                var vl = parseInt(palindrome($('.total_last_year_vl').text()));
                var sl = parseInt(palindrome($('.total_last_year_sl').text()));
                var thisYearVl = parseInt(palindrome($('.this-year-vl').text()));
                var thisYearSl = parseInt(palindrome($('.this-year-sl').text()));
                var vlTotal = (vl + thisYearVl) / 100;
                var slTotal = (sl + thisYearSl) / 100;
                $('.total_this_year_vl').text(vlTotal);
                $('.total_this_year_sl').text(slTotal);
                var lessVl = parseFloat(palindrome($('.leave_record_vl').text()));
                var lessSl = parseFloat(palindrome($('.leave_record_sl').text()));
                $('.balance_vl').text(vlTotal - lessVl);
                $('.balance_sl').text(slTotal - lessSl);
                var balanceVl = $('.balance_vl').text();
                var balanceSl = $('.balance_sl').text();
                balanceVl = parseInt(palindrome(balanceVl));
                balanceSl = parseInt(palindrome(balanceSl));
                var totalBalance = ((balanceSl + balanceVl) / 100).toFixed(2);
                $('.grand_total').text(totalBalance);
                console.log(palindrome(totalBalance.toString()))
                totalBalance = palindrome(totalBalance.toString())
                var vlCount = parseInt($('.leave_record_vl').text())
                var slCount = parseInt($('.leave_record_sl').text())
                checkVlSlCount(vlCount,slCount,id,totalBalance,balanceVl,balanceSl)
                console.log(checkVlSlCount(vlCount,slCount,id))
                // if(checkVlSlCount(vlCount,slCount,id) === false){
                //     storeTotalEarned(totalBalance,balanceVl,balanceSl);
                // }
            }
        });
        function palindrome(str) {
            str = str.replace(/[\s,\.]+/g, '');
            return str;
        }
        function getEearnedLastYear(){
            $.ajax({
                url: "{{route('leave.get.earned.last.year')}}",
                type: "post",
                dataType: "json",
                data: {id:id},
                success: function(data){
                    for(const property in data.data){
                        if(property === 'sl'){
                            $('.total_last_year_sl').text(data.data['sl'] / 100)
                        }
                        if(property === 'sl'){
                            $('.total_last_year_vl').text(data.data['vl'] / 100)
                        }
                    }
                }
            });
        }
        function storeTotalEarned(totalBalance,vl,sl,vl_count,sl_count){
            let storeUrl = "{{route('store.total.earned', ':id')}}";
            storeUrl = storeUrl.replace(':id',id)
            $.ajax({
                url: storeUrl,
                type: "post",
                data: {
                    totalBalance: totalBalance,
                    vl: vl,
                    sl: sl,
                    vl_count: vl_count,
                    sl_count: sl_count,
                },
                dataType: "json",
                success: function(data){
                    console.log("success");
                }
            });
        }
        function checkVlSlCount(vl,sl,id,totalBalance,balanceVl,balanceSl){
            let getUrl = "{{route('check.leave.count',':id')}}";
            getUrl = getUrl.replace(':id',id);
            let v;
            $.ajax({
                url: getUrl,
                type: "post",
                data: {
                    sl_count: sl,
                    vl_count: vl,
                },
                dataType: "json",
                success: function(data){
                    if(data.data == null){
                        storeTotalEarned(totalBalance,balanceVl,balanceSl,vl,sl)
                    }
                }
            });
        }
    })
</script>
</html>