<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <div style="text-align: center;">
        <div>ALTURAS GROUP OF COMPANIES</div>
        <div>Head Office - Custodian Department</div>
        <div>Special External GC Releasing Report</div>
    </div>
    <table width="100%">
        <tr>
            <td align="left">Request No : {{ $reqNo }}</td>
            <td align="right">Date Approved: {{ $request->dti_approveddate }}</td>
        </tr>
        <tr>
            <td align="left">Customer No: </td>
            <td align="right">Date Needed: {{ $request->dti_dateneed }}</td>
        </tr>
    </table>
    <table width="100%" style="border-collapse: collapse; border: 1px solid black; margin: 10px">
        <thead>
            <tr>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Name Ext</th>
                <th>Denomination</th>
                <th>Barcode</th>
            </tr>
        </thead>
        <tbody>
            {{-- {{$barcodes}} --}}
            @foreach ($barcodes as $item)
                <tr>
                    <td style="border: 1px solid black; text-align: center; padding: 2px;">{{ $item->lname }}</td>
                    <td style="border: 1px solid black; text-align: center; padding: 2px;">{{ $item->fname }}</td>
                    <td style="border: 1px solid black; text-align: center; padding: 2px;">{{ $item->mname }}</td>
                    <td style="border: 1px solid black; text-align: center; padding: 2px;">{{ $item->extname }}</td>
                    <td style="border: 1px solid black; text-align: center; padding: 2px;">{{ $item->dti_denom }}</td>
                    <td style="border: 1px solid black; text-align: center; padding: 2px;">{{ $item->dti_barcode }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div style="width: 100%; font-size: 12px; text-align: center; margin-top: 70px;">
        <div style="display: inline-block; text-align: center; width: 40%; margin-right: 10%;">
            Prepared by:<br><br>
            _______________________________<br>
            (Signature Over Printed Name)
        </div>
        <div style="display: inline-block; text-align: center; width: 40%;">
            Received by:<br><br>
            _______________________________<br>
            (Signature Over Printed Name)
        </div>
    </div>

    <div
        style="position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 15px; color: #666; margin-top: 20px;">
        <span class="totalPages"> {{$reprintedLabel}} {{$count}} </span>
    </div>

    {{-- {{$barcodes}} --}}
</body>

</html>
