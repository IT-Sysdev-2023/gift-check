<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GC EOD Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
        }

        .page-break {
            page-break-after: always;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            text-align: center;
        }

        .header {
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 20px;
            text-transform: uppercase;
            margin: 0;
        }

        .header h2 {
            font-size: 16px;
            margin: 5px 0;
        }

        .report-dates {
            font-size: 12px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        td.left {
            text-align: left;
        }

        .total {
            font-weight: bold;
        }
    </style>
</head>

<body>
        <div class="container">
            <div class="header">
                <h1>ALTURAS GROUP OF COMPANIES</h1>
                <h3>GC EOD Report</h3>
            </div>

            <div class="report-dates">

                @if (!empty($data['header']['transactionDate']))
                    <p>Transaction Date: {{$data['header']['transactionDate']}}</p>
                @endif

                <p>Report Created: {{$data['header']['reportCreated']}}</p>
            </div>
            <table>
                <tr>
                    <th class="left">Date</th>
                    <th>Eod Number</th>
                    <th>Eod By</th>
                </tr>

                @foreach ($data['records'] as $item)
                    <tr>
                        <td class="left">{{$item->date}}</td>
                        <td>{{$item->ieod_num}}</td>
                        <td>{{$item->fullname}}</td>
                    </tr>
                @endforeach
            </table>

        </div>
</body>

</html>