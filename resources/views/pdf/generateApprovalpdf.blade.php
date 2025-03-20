<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: end;
        }

        .flex {
            display: flex;
            flex-direction: row;
        }

        .w-1-2 {
            width: 50%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-family: Arial, sans-serif;
        }

        th {
            color: black;
            padding: 2px;
            text-align: center;
            border: 1px solid #ddd;
        }

        td {
            padding: 2px;
            text-align: center;
            border: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }
    </style>
</head>

<body>
    <p class="text-center">Alturas Group Of Companies</p>
    <p class="text-center">Head Office - Finance Department</p>
    <p class="text-center">Special {{ $gcType }} GC Approval Report</p>

    <div>
        <div class="flex">
            <div>
                Request #: {{ $requestData['spexgc_num'] }}
            </div>
            <div class="text-end">
                Date Approved: {{ now()->format('F d, Y') }}
            </div>
        </div>
        <div class="flex">
            <div>
                Customer: {{ $requestData['spcus_acctname'] }}
            </div>
            <div class="text-end">
                Date Needed: {{ \Carbon\Carbon::parse($requestData['spexgc_dateneed'])->format('F d, Y') }}
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Name Ext.</th>
                <th>Denomination</th>
                <th>Barcode</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $item): ?>
            <tr>
                <td><?php    echo $item['spexgcemp_fname']; ?></td>
                <td><?php    echo $item['spexgcemp_lname']; ?></td>
                <td><?php    echo $item['spexgcemp_mname']; ?></td>
                <td><?php    echo $item['spexgcemp_extname']; ?></td>
                <td><?php    echo $item['spexgcemp_denom']; ?></td>
                <td><?php    echo $item['spexgcemp_barcode']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style='margin-top: 20px'>
        <p>Payment Type: {{ $paymentType }}</p>
        <p>Amount Received: {{ $requestData['spexgc_payment'] }}</p>
        <p>Total No. of GC: {{ $requestData['special_external_gcrequest_items_has_many'][0]['specit_qty'] }}</p>
        <p>Total GC Amount: {{ number_format($requestData['total'], 2) }}</p>
        <p>AR #: {{ $requestData['spexgc_payment_arnum'] }}</p>
    </div>
    <div style="position: absolute; bottom: 0px; width: 300px;">
        <div>
            Prepared By:
        </div>
        <div style="margin-left: 20px;">
            <div style="text-align: center;">{{$signitures['preparedBy']}}</div>
            <hr>
            <div style="text-align: center;">Signiture over Printed Name</div>
        </div>

        <div style="visibility: hidden">
            Prepared By:
        </div>
        <div style="margin-left: 20px;">
            <div style="text-align: center; visibility: hidden">Name</div>
            <hr style="visibility: hidden;">
            <div style="text-align: center; visibility: hidden">Signature over Printed Name</div>
        </div>
    </div>
    <div style="position: absolute; bottom: 0px; width: 300px; margin-left:50% ;">
        <div>
            Checked By:
        </div>
        <div style="margin-left: 20px;">
            <div style="text-align: center;">{{$signitures['checkedBy']}}</div>
            <hr>
            <div style="text-align: center;">Signiture over Printed Name</div>
        </div>
        <div>
            Approved By:
        </div>
        <div style="margin-left: 20px;">
            <div style="text-align: center;">{{$signitures['approvedBy']}}</div>
            <hr>
            <div style="text-align: center;">Signiture over Printed Name</div>
        </div>
    </div>
</body>

</html>