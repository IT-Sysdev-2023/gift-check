<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['company']['report']}}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .report {
            text-align: center;
        }

        .details, .gc-codes, .summary, .signatures {
            width: 100%;
            margin: 20px 0;
        }

        .details {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
        }

        .gc-codes td {
            padding: 5px;
            text-align: center;
        }

        .gc-codes {
            margin-bottom: 10px;
        }

        .signatures {
            width: 100%;
            margin-top: 50px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        .signature-table td {
            width: 33%;
            text-align: center;
            vertical-align: top;
            padding-top: 30px;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 10px auto 0;
        }

        h4 {
            padding-bottom: 20px;
        }

        h2, h3, h4 {
            margin: 5px 0;
        }

        .denomination p {
            border: 0.5px solid #000;
            padding: 8px;
            text-align: left;
        }

        .summary p {
            text-align: left;
        }

        .denomination .result {
            text-align: right;
        }
        .signature-label{
            text-align: left;
            padding-bottom: 15px
        }
    </style>
</head>
<body>
    <div class="report">
        <h2>{{ $data['company']['name']}}</h2>
        <h3>{{ $data['company']['department']}}</h3>
        <h4>{{ $data['company']['report']}}</h4>

        <table class="gc-codes">
            <tr>
                <td><strong>Location:</strong> {{ $data['company']['location'] }}</td>
                <td><strong>Store:</strong> {{ $data['store']}}</td>
            </tr>
            <tr>
                <td><strong>GC Rel. No.:</strong>{{ $data['gc_rel_no']}} </td>
                <td><strong>Released Date:</strong> {{ $data['date_released']}}</td>
            </tr>
        </table>

        <div class="denomination">
            @foreach ($data['barcode'] as $denomination => $items)
            <p><strong>Denomination: {{ $denomination }}</strong></td>
                <table class="gc-codes">
                    @foreach ($items as $index => $barcode)
                        @if ($index % 5 == 0)
                            <tr>
                        @endif
                        <td>{{ $barcode->re_barcode_no }}</td>
                        @if (($index + 1) % 5 == 0 || $index + 1 == count($items))
                            </tr>
                        @endif
                    @endforeach
                </table>
                <p class="result"><strong>No of GC: </strong>{{count($items)}} pcs</p>
            @endforeach
            
        </div>

        <div class="summary">
            <p><strong>Releasing Type:</strong> {{ $data['releasing_type'] }}</p>
            <p><strong>Total No. of GC:</strong> {{ $data['total_number_of_gc']}} pcs</p>
            <p><strong>Total GC Amount:</strong> {{ $data['total_gc_amount']}}</p>
            <p><strong>Payment Type:</strong> {{ $data['payment_type']}}</p>
            @if($data['amount_receive'])
            <p><strong>Amount Received:</strong> {{$data['amount_receive']}}</p>
            @endif
            @if($data['bank_name'])
            <p><strong>Bank Name:</strong> {{$data['bank_name']}}</p>
            @endif
            @if($data['bank_account'])
            <p><strong>Bank Account #:</strong> {{$data['bank_account']}}</p>
            @endif
            @if($data['check'])
            <p><strong>Check #:</strong> {{$data['check']}}</p>
            @endif
            @if($data['check_amount'])
            <p><strong>Check Amount:</strong> {{$data['check_amount']}}</p>
            @endif
            @if($data['customer'])
            <p><strong>Customer:</strong> {{$data['customer']}}</p>
            @endif
        </div>

        <table class="signature-table">
            <tr>
                <td>
                    <p class="signature-label">Received by:</p>
                    <p><strong>{{ $data['received_by']}}</strong></p>
                    <div class="signature-line">(Signature over Printed name)</div>
                </td>
                <td>
                    <p class="signature-label">Released by:</p>
                    <p><strong>{{ $data['released_by']}}</strong></p>
                    <div class="signature-line">(Signature over Printed name)</div>
                </td>
                <td>
                    <p class="signature-label">Checked by:</p>
                    <p><strong>{{ $data['checked_by']}}</strong></p>
                    <div class="signature-line">(Signature over Printed name)</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
