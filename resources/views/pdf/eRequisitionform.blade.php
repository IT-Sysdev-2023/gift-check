<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #ffff;
        }

        tr:nth-child(even) {
            background-color: #ffff;
        }

        tr:hover {
            background-color: #ffff;
        }

        .header {
            text-align: center;
            padding: 10px;
            font-weight: bold;
        }

        .content div {
            margin: 10px 0;
        }

        .checkedBy {
            width: 300px;
            height: 100px;
            position: absolute;
            bottom: 0px;
        }
        .preparedBy {
            width: 300px;
            height: 100px;
            position: absolute;
            bottom: 0px;
            right: 0px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div>Marketing Department</div>
        <div>ALTURAS GROUP OF COMPANIES</div>
        <div>GC E-Requisition</div>
    </div>
    <div class="content">
        <div style="text-align:left">
            <strong>E-Req.No :</strong> {{$data['reqNum']}}
        </div>
        <div style="text-align:left">
            <strong>Date Requested :</strong> {{$data['dateReq']}}
        </div>
        <div style="text-align:left">
            <strong>Request :</strong> Request for gift cheque printing as per breakdown provided below.
        </div>

        <table>
            <thead>
                <tr>
                    <th>Denomination</th>
                    <th>Quantity</th>
                    <th>Barcode No. Start</th>
                    <th>Barcode No. End</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($barcodes as $item)
                    <tr>
                        <td>{{$item['denomination']}}</td>
                        <td>{{$item['pe_items_quantity']}}</td>
                        <td>{{$item['barcodeStart']}}</td>
                        <td>{{$item['barcodeEnd']}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div>
            <div>
                <div style="font-size: 16px; margin-top:30px">
                    <strong>Supplier Information</strong>
                </div>
            </div>
            <table border="1" cellpadding="5">
                <tr>
                    <th>Company Name</th>
                    <td>{{$supplier['gcs_companyname']}}</td>
                </tr>
                <tr>
                    <th>Contact Person</th>
                    <td>{{$supplier['gcs_contactperson']}}</td>
                </tr>
                <tr>
                    <th>Contact #</th>
                    <td>{{$supplier['gcs_contactnumber']}}</td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td>{{$supplier['gcs_address']}}</td>
                </tr>
            </table>

            <div class="checkedBy">
                <div style="text-align:left">Checked by:</div>
                <div style="text-align:center">{{$data['checkedBy']}}</div>
                <hr style="border: 0.5px solid #000; width: 100%; text-align: center;">
                <div style="text-align:center">Signature over Printed Name</div>
            </div>
            <div class="preparedBy">
                <div style="text-align:left">Approved by:</div>
                <div style="text-align:center">{{$data['approvedBy']}}</div>
                <hr style="border: 0.5px solid #000; width: 100%; text-align: center;">
                <div style="text-align:center">Signature over Printed Name</div>
            </div>
        </div>
    </div>
</body>

</html>