
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['company']['report']}}</title>
</head>
<body>
    <div class="report">
        <h2>{{ $data['company']['name']}}</h2>
        <h3>{{ $data['company']['department']}}</h3>
        <h4>{{ $data['company']['report']}}</h4>

        <table class="gc-codes">
            <tr>
                <td><strong>GC Rel. No.:</strong>{{ $data['subheader']['gc_rel_no']}} </td>
                <td><strong>Released Date:</strong> {{ $data['subheader']['date_released']}}</td>
            </tr>
            <tr>
                <td><strong>Customer:</strong> {{ $data['subheader']['customer'] }}</td>
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
                        <td>{{ $barcode['barcode'] }}</td>
                        @if (($index + 1) % 5 == 0 || $index + 1 == count($items))
                            </tr>
                        @endif
                    @endforeach
                </table>
                <p class="result"><strong>No of GC: </strong>{{count($items)}} pcs</p>
            @endforeach
            
        </div>

        <div class="summary">
            @foreach($data['summary'] as $title => $items)
                @if($items)
                <p><strong>{{Str::headline($title)}}:</strong> {{ $items}}</p>
                @endif
            @endforeach
        </div>

        <table class="signature-table">
            <tr>
                @foreach($data['signatures'] as $title => $name)
                <td>
                    <p class="signature-label">{{ Str::headline($title) }}:</p>
                    <p><strong>{{ $name }}</strong></p>
                    <div class="signature-line">(Signature over Printed name)</div>
                </td>
                @endforeach
            </tr>
        </table>
    </div>
</body>
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
</html>
