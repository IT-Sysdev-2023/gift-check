<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPGC APPROVED Report</title>
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

        .title {
            text-transform: uppercase;
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>ALTURAS GROUP OF COMPANIES</h1>
            <h3>Head Office - Finance Department</h3>
            <h3>Special External GC Report-Approval</h3>
        </div>

        <div class="report-dates">

            @if (!empty($data['header']['transactionDate']))
                <p>Transaction Date: {{$data['header']['transactionDate']}}</p>
            @endif

            <p>Report Created: {{$data['header']['reportCreated']}}</p>
        </div>


        <p class="title"><strong>Per Customer</strong></p>
        <table>
            <tr>
                <th class="left">Date Requested</th>
                <th>Company</th>
                <th>Approval #</th>
                <th>Total Amount</th>
            </tr>


            @foreach ($data['records']['perCustomer'] as $item)
                <tr>
                    <td class="left">{{$item->datereq}}</td>
                    <td>{{$item->spcus_acctname}}</td>
                    <td>{{$item->spexgc_num}}</td>
                    <td>{{$item->totDenom}}</td>
                </tr>
            @endforeach
        </table>

        <p class="title"><strong>Per Barcode</strong></p>
        <table>
            <tr>
                <th></th>
                <th class="left">Date Requested</th>
                <th>Barcode</th>
                <th>Denom</th>
                <th>Customer</th>
                <th>Voucher</th>
                <th>Approval #</th>
                <th>Date Approved</th>
            </tr>
            @foreach ($data['records']['perBarcode'] as $item)
                <tr>
                    <td>{{$item->count}}</td>
                    <td class="left">{{$item->datereq}}</td>
                    <td>{{$item->spexgcemp_barcode}}</td>
                    <td>{{$item->spexgcemp_denom}}</td>
                    <td>{{$item->fullName}}</td>
                    <td>{{$item->voucher}}</td>
                    <td>{{$item->spexgc_num}}</td>
                    <td>{{$item->daterel}}</td>
                </tr>
            @endforeach
        </table>
        <p>Total No. of Gc: {{$data['total']['noOfGc']}}</p>
        <p>Total GC Amount: {{$data['total']['gcAmount']}}</p>
    </div>
</body>

</html>