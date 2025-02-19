<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Institution GC Releasing Report</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>{{$data['company']['name']}}</h1>
        </div>

        <h3 >{{$data['company']['department']}}</h3>
        <h3 style="padding-bottom: 28px">{{$data['company']['report']}}</h3>

        <table class="gc-codes">
            <tr>
                <td><strong>SGC Req #: </strong>{{$data['subheader']['sgcReq']}}</td>
                <td><strong>Date Received: </strong>{{$data['subheader']['dateReceived']}}</td>
            </tr>
            <tr>
                <td><strong>Customer: </strong>{{$data['subheader']['customer']}}</td>
                <td><strong>Account Name: </strong>{{$data['subheader']['accountName']}}</td>
            </tr>
        </table>

        <table class="gc-table">
            <thead>
                <tr>
                    <th>Denomination</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['denom'] as $denom)
                <tr>
                    <td>{{$denom['denomination']}}</td>
                    <td>{{$denom['qty']}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <p><strong>Total No. of GC: </strong>{{$data['totalGcQty']}}</p>
            <p><strong>Total GC Amount: </strong>{{$data['totalGcAmount']}}</p>
        </div>

        <div class="signature">
            <p class="received-by">Received by:</p>
            <p class="sig-line">{{$data['receivedBy']}}</p>
            <p>(Signature over Printed name)</p>
        </div>
    </div>
</body>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 80%;
        margin: 20px auto;
        background-color: white;
        padding: 20px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }

    .header {
        text-align: center;
    }

    .header h1 {
        margin: 0;
        font-size: 24px;
    }

    .header p {
        margin: 5px 0;
        font-size: 14px;
        color: #555;
    }

    h3 {
        text-align: center;
        margin: 5px 0px;
    }

    .gc-codes td {
            padding: 5px;
            text-align: center;
        }

    .gc-codes {
        margin-bottom: 10px;
        width: 100%;
        border-spacing: 0 10px;
        }

    .gc-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .gc-table th, .gc-table td {
        border: 1px solid #000;
        padding: 8px;
        text-align: center;
    }

    .totals {
        margin: 20px 0;
    }

    .totals p {
        font-size: 14px;
        margin: 5px 0;
    }

    .signature {
        margin-top: 40px;
        text-align: right;
    }

    .signature p {
        margin: 5px 0;

        font-size: 14px;
    }

    .sig-line {
        margin-top: 40px;
        font-weight: bold;
        /* float: right; */
        text-align: center;
        text-transform: uppercase;
        border-bottom: 1px solid #000;
        width: 200px;
        display: inline-block;
        margin-left: 0;
        margin-right: 0;
        margin: 40px auto 0;
    }

    .received-by{
        padding-bottom: 30px;
        padding-right:180px;
    }

    </style>
</html>
