@extends('layouts.admin')
@section('Heading')
  @if(Auth::user()->isAllowed('LeaveController:adminCreate'))
  <button type="button"  onclick="printDiv()" class="btn btn-info btn-rounded m-t-10 float-right"><span class="fas fa-print"></span> Print Receivables</button>
  @endif
  <h3 class="text-themecolor">Receivables</h3>
  <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
      <li class="breadcrumb-item active">Employee Mngmt</li>
      <li class="breadcrumb-item active">Receivables</li>
  </ol>
@stop
@section('content')
<div class="card">
  <div class="card-body">
    <table id="rcvTbl" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
      <thead>
        <tr>  
          <th>Name</th>
          <th>Particulars</th>
          <th>Department</th>
        </tr>
      </thead>
      <tbody style="border:1px solid #dddd;">
          @foreach($rec as $item=>$value)
          <tr>
            <td style="border:1px solid #dddd;">
               {{ $item  }}
            </td>
            <td style="border:1px solid #dddd;"">
              @foreach($value as $r)
               {{$r['particulars'] }} <br>
              @endforeach
            </td>
            <td style="border:1px solid #dddd;">
              {{$value[0]['designated_dep']}}
            </td>
          </tr>
          @endforeach
        
      </tbody>
    </table>
  </div>
</div>

<script>
function printDiv() {
  var divToPrint = document.getElementById('rcvTbl');
  newWin = window.open("");
  var printHeader="";

  printHeader += "<center> <label style='font-size: 20px; font-weight: 600;'> LANAO DEL SUR ELECTRIC COOPERATIVE, INC. </label> <br>";
  printHeader += "<label> Brgy. Gadongan, Marawi City, Philippines </label> <br>";
  printHeader += "<label> itlasureco@gmail.com </label> <br><br>";
  printHeader += "<label style='font-size: 18px; font-weight: 600;'> Job Order Personnel Accounts Receivables from Lasureco </label> </center> <br>";

  newWin.document.write(printHeader);
  newWin.document.write(divToPrint.outerHTML);
  newWin.print();
  newWin.close();
}
 
</script>
@stop