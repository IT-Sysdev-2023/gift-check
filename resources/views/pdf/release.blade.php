<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
     <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th {
            padding: 10px;
            text-align: center;
        }
        td {
            padding: 10px;
            text-align: left;
            font-size: x-small;
            font-weight: bold
        }
        .center-text {
            text-align: center; 
            font-size: small;
            margin-top: 10px;
        }
    </style>
</head>
{{-- {{ $releasePerCustomer }}  {{ $releasePerBarcode }} --}}
<body>
    <h1 class="center-text">ALTURAS GROUP OF COMPANIES</h1>
    <h1 class="center-text">HEAD OFFICE FINANCE DEPARTMENT</h1>
    <h1 class="center-text">SPECIAL EXTERNAL GC REPORT- RELEASE</h1>
    <h1 class="center-text">PER CUSTOMER</h1>

    <table>
        <thead>
            <tr>
                <th>Date Requested</th>
                <th>Company</th>
                <th>Releasing Number</th>
                <th>Denomination</th>
                <th>Date Release</th>
            </tr>

        </thead>
        <tbody>
            @foreach ($releasePerCustomer as $customer)
                <tr>
                    <td>{{ $customer->datereq }}</td>
                    <td>{{ $customer->spcus_companyname }}</td>
                    <td>{{ $customer->spexgc_num }}</td>
                    <td>{{ $customer->totdenom }}</td>
                    <td>{{ $customer->daterel }}</td>
                </tr>
                @endforeach
        </tbody>
    </table>

   <h1 class="center-text">ALTURAS GROUP OF COMPANIES</h1>
    <h1 class="center-text">HEAD OFFICE FINANCE DEPARTMENT</h1>
    <h1 class="center-text">SPECIAL EXTERNAL GC REPORT- RELEASE</h1>
    <h1 class="center-text">PER BARCODE</h1>
    
    <table>
        <thead>
            <tr>
                <th>Date Request</th>
                <th>Denomination</th>
                <th>Customer </th>
                <th>Barcode</th>
                <th>Approval #</th>
                <th>Date Release</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($releasePerBarcode as $barcode)
                <tr>
                    <td>{{ $barcode->datereq }}</td>
                    <td>{{ $barcode->spexgcemp_denom }}</td>
                    <td>{{ $barcode->spexgcemp_lname }}</td>
                    <td>{{ $barcode->spexgcemp_barcode }}</td>
                    <td>{{ $barcode->spexgc_num }}</td>
                    <td>{{ $barcode->daterel }}</td>
                </tr>
                @endforeach
        </tbody>
    </table>
</body>
</html>