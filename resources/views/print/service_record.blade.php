<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    
</head>
<body>
    <center><h1 style="margin-top: 5%">SERVICE RECORD</h1></center>
    <br>
    <p>NAME     : {{ ucfirst($employee->firstname).' '.ucfirst($employee->lastname) }}</p>
    <p>ADDRESS  : {{ ($employee->current_address == NULL) ? '' : $employee->current_address }}</p><br>
    
    <p style="text-indent: 30px;"> This is to certify that the above-named person has rendered service in this office as shown by his service record below, to wit:</p>

    <table style="width: 100%;margin-top: 5%">
        <thead>
            <tr>
                <th>REGISTRED NAME</th>
                <th>POSITION</th>
                <th>SALARY</th>
                <th>STATUS</th>
                <th>INCLUSIVE DATES</th>
            </tr>
        </thead>
        <tbody>
                @forelse($appointment as $item)
                <tr style="text-align: center">
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['position'] }}</td>
                    <td>{{ $item['salary'] }}</td>
                    <td> IDK</td>
                    <td>{{ $item['inclusive_dates'] }}</td>
                </tr>
                @empty
                <tr >
                    <td style="text-align: center;" colspan="5"><p style="color: red"> --- No Record --- </p></td>
                </tr>
                @endforelse
        </tbody>
    </table>
    <div style="margin-top: 5%"></div>
    <p style="text-indent: 30px;">Issued this __________________________ at Capitol Satelite Office, Marawi City.</p>
    <table style="width: 100%;margin-top: 4%">
        <tr >
            <td><p></p></td>
            <td><p>Prepared & Checked by:</p></td>
            <td><p></p></td>
            <td><p></p></td>
            <td><p>Certified by:</p></td>
        </tr>
    </table>
    

</body>
</html>