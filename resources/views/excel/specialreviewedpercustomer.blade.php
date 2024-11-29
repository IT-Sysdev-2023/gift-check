<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th style="font-weight: bold">
                    ALTURAS GROUP OF COMPANIES
                </th>
            </tr>
            <tr>
                <th style="font-weight: bold">
                    HEAD OFFICE FINANCE DEPARTMENT
                </th>
            </tr>
            <tr>
                <th style="font-weight: bold">
                    SPECIAL EXTERNAL GC REPORT- APPROVAL
                </th>
            </tr>
            <tr>
                <th>
                    {{ $date }}
                </th>
            </tr>
        </thead>
    </table>
    <table>
        <thead>
            <tr>
                <th style="border: 1px solid black; font-weight: bold">DATE</th>
                <th style="border: 1px solid black;  font-weight: bold">COMPANY</th>
                <th style="border: 1px solid black;  font-weight: bold">APPROVAL</th>
                <th style="border: 1px solid black;  font-weight: bold">TORAL DENOM</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td style="border: 1px solid black; font-size: 10px">{{ $item->date }}</td>
                    <td style="border: 1px solid black; font-size: 10px">{{ $item->acct }}</td>
                    <td style="border: 1px solid black; font-size: 10px">{{ $item->num }}</td>
                    <td style="border: 1px solid black; font-size: 10px">{{ $item->denom }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
