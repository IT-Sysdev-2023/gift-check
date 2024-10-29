<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Store Reports</title>
</head>

<body>
    <div class="header">
        <h4 class="agc" style="margin-bottom: -17px">ALTURAS GROUP OF COMPANIES</h4>
        <h6 style="margin-bottom: -10px">INTERNAL AUDIT DEPARTMENT</h6>
        <h5 style="margin-bottom: -17px">Gift Checks Audit</h5>
        <h5>{{ $data->date }}</h5>
    </div>
    <table class="flex" width="100%" style="margin-bottom: 1px;">
        <tr>
            <td style="text-align: left;">{{ empty($data->addedgc) ? '' : ' Additional Gift Checks' }}</td>
            <td style="text-align: right; font-weight: 600; font-size: 12px; letter-spacing: 1px; color: #024CAA;">
                Beginning Balance: {{ number_format($data->begbal, 2) }}
            </td>
        </tr>
    </table>
    @if (!empty($data->addedgc))
        <table class="table">
            <thead>
                <tr>
                    <th>Denomination</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Quantity</th>
                    <th></th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data->addedgc as $key => $item)
                    <tr>
                        <td>{{ $item->denom }}</td>
                        <td>{{ $item->barcodest }}</td>
                        <td>{{ $item->barcodelt }}</td>
                        <td>{{ number_format($item->count) }}</td>
                        <td> = </td>
                        <td>{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if (!empty($data->gcsold))
        <p style="margin-bottom: 1px">Sold Gift Checks</p>
        <table class="table">
            <thead>

                <tr>
                    <th>Denomination</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Quantity</th>
                    <th></th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data->gcsold as $key => $item)
                    <tr>
                        <td>{{ $item->denom }}</td>
                        <td>{{ $item->barcodest }}</td>
                        <td>{{ $item->barcodelt }}</td>
                        <td>{{ number_format($item->count) }}</td>
                        <td> = </td>
                        <td>{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p style="text-align: right; font-weight: 600; font-size: 12px; letter-spacing: 1px; color: #024CAA">Total:
            {{ number_format($data->gcsoldbal, 2) }}</p>
        <hr>
    @endif

    @if (!empty($data->unusedgc))
        <p style="margin-bottom: 1px">Smart of Unused Gift Checks</p>
        <table class="table">
            <thead>
                <tr>
                    <th>Denomination</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Quantity</th>
                    <th></th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data->unusedgc as $key => $item)
                    <tr>
                        <td>{{ $item->denom }}</td>
                        <td>{{ $item->barcodest }}</td>
                        <td>{{ $item->barcodelt }}</td>
                        <td>{{ number_format($item->count) }}</td>
                        <td> = </td>
                        <td>{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p style="text-align: right; font-weight: 600; font-size: 12px; letter-spacing: 1px; color: #024CAA">Total:
            <span> {{ number_format($data->unusedbal, 2) }}</span>
        </p>
    @endif

    <table class="flex" width="100%" style="margin-bottom: 1px; margin-top: 60px">
        <tr>
            <td style="text-align: center; width:50%;">
                <div style="width:70%;  margin: auto">
                    <p style="margin-bottom: 40px;">Audited by:</p>
                    <hr style="margin-bottom: -3px"/>
                    <span style="font-size: 12px">Audit Staff</span>
                </div>
            </td>
            <td style="text-align: center; width:50%;">
                <div  style="width:70%; margin: auto">
                    <p style="margin-bottom: 40px;">
                        Confirmed by
                    </p>
                    <hr  style="margin-bottom: -3px"/>
                    <span style="font-size: 12px">Treasury in-Charge</span>
                </div>
            </td>
        </tr>
    </table>
    <p style="margin-bottom: 35px; margin-top: 35px; text-align: center;">Noted By:</p>
    <table class="flex" width="100%" style="margin-bottom: 1px; margin-top: 30px">
        <tr>
            <td style="text-align: center; width:50%;" >
                <div style="width:70%; margin: auto">
                    <hr style="margin-bottom: -3px"/>
                    <span style="font-size: 12px">AGC IAD - Head Staff</span>
                </div>
            </td>
            <td style="text-align: center; width:50%;">
                <div  style="width:70%; margin: auto">
                    <hr  style="margin-bottom: -3px"/>
                    <span style="font-size: 12px">Company Cashier</span>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>
<style>
    .header {
        text-align: center;
        font-family: "Gill Sans", sans-serif;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        font-family: serif;
        font-size: 13px;
    }

    /* Table header styling */
    .table th {
        background-color: #e0e0e0;
        color: rgb(0, 0, 0);
        padding: 6px;
        text-align: center;
        border: 1px solid #2c2c2c;
    }

    /* Table row styling */
    .table td {
        padding: 4px;
        text-align: center;
        border: 1px solid #2c2c2c;
    }

    .table tr:hover {
        background-color: #2c2c2c;
    }
</style>
