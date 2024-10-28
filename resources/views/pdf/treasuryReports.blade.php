<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GC Sales Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
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
            <h2>{{$data['header']['store']}}</h2>
            <h1>ALTURAS GROUP OF COMPANIES</h1>
            <h3>GC Sales Report</h3>
        </div>

        <div class="report-dates">
            <p>Transaction Date: September 29, 2017 - October 14, 2024</p>
            <p>Report Created: {{$data['header']['reportCreated']}}</p>
        </div>

        <table>
            <tr>
                <th colspan="5">Cash Sales</th>
            </tr>
            <tr>
                <th class="left">GC Denomination</th>
                <th>GC Sold</th>
                <th>Sub Total</th>
                <th>Line Disc.</th>
                <th>Net</th>
            </tr>
            <tr>
                <td class="left">500.00</td>
                <td>5768</td>
                <td>2,884,000.00</td>
                <td>0.00</td>
                <td>2,884,000.00</td>
            </tr>
            <tr>
                <td class="left">1,000.00</td>
                <td>316</td>
                <td>316,000.00</td>
                <td>0.00</td>
                <td>316,000.00</td>
            </tr>
            <tr>
                <td class="left">2,000.00</td>
                <td>8</td>
                <td>16,000.00</td>
                <td>0.00</td>
                <td>16,000.00</td>
            </tr>
            <tr>
                <td class="left">5,000.00</td>
                <td>0</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
            </tr>
            <tr class="total">
                <td colspan="4" class="left">Total Net:</td>
                <td>3,216,000.00</td>
            </tr>
        </table>


        <table>
            <tr>
                <th colspan="5">AR</th>
            </tr>
            <tr>
                <th class="left">GC Denomination</th>
                <th>GC Sold</th>
                <th>Sub Total</th>
                <th>Line Disc.</th>
                <th>Net</th>
            </tr>
            <tr>
                <td class="left">500.00</td>
                <td>0</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
            </tr>
            <tr>
                <td class="left">1,000.00</td>
                <td>0</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
            </tr>
            <tr>
                <td class="left">2,000.00</td>
                <td>0</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
            </tr>
            <tr>
                <td class="left">5,000.00</td>
                <td>0</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
            </tr>
            <tr class="total">
                <td colspan="4" class="left">Total Net:</td>
                <td>0.00</td>
            </tr>
        </table>

        <table>
            @foreach($data['footer'] as $title => $name)
                <tr class="total">
                    <td colspan="4" class="left">{{Str::headline($title)}}:</td>
                    <td>{{$name}}</td>
                </tr>
            @endforeach
        </table>
    </div>
</body>

</html>