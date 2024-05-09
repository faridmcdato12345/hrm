<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <form action="{{route('employee.add.pgsql.store')}}" method="post">
        {{csrf_field()}}
        <input type="text" placeholder="firstname" name="first_name">
        <input type="text" placeholder="lastname" name="last_name">
        <input type="text" placeholder="nickname" name="nickname">
        <select name="gender" id="gender">
            <option value="M">Male</option>
            <option value="F">Female</option>
        </select>
        <button type="submit">Save</button>
    </form>
</body>
</html>