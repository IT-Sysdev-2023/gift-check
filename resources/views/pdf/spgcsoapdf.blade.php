<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div>
        <div style="display:flex; justify-content: center;">
            <img src="{{ asset('alturas.jpg') }}" alt="company logo">
        </div>
        <div style="text-align: center;">ALTURAS GROUP OF COMPANIES</div>
        <div style="text-align: center;">HEAD OFFICE - TREASURY DEPARTMENT</div>
        <div style="text-align: center;">SPECIAL GIFT CHECK RELEASING REPORT</div>
    </div>
    <div style="margin-top: 30px;">
        <table style="width: 100%;">
            <td style="width: 50%;">
                <div>Request No: {{ $data['spexgc_num'] }}</div>
            </td>
            <td style="width: 50%;">
                <div>Date Requested: {{ $data['spexgc_datereq'] }}</div>
            </td>
        </table>
        <table style="width: 100%;">
            <td style="width: 50%;">
                <div>Date Released: {{ $data['spexgc_datereleased'] }}</div>
            </td>
        </table>

    </div>
    <div>
        <p style="text-align: center;">STATEMENT OF ACCOUNT</p>
        <div style="margin-top: 30px">
            <table style="width: 100%;">
                <td style="width: 50%;">
                    <div>Date Generated:{{ $data['dateGenerated'] }}</div>
                </td>
                <td style="width: 50%;">
                    <div>SOA Reference #:</div>
                </td>
            </table>
            <table style="width: 100%;">
                <td style="width: 50%;">
                    <div>Customer Name:{{ $data['customerName'] }}</div>
                </td>
            </table>
            <table style="width: 100%;">
                <td style="width: 50%;">
                    <div>TIN:</div>
                </td>
                <td style="width: 50%;">
                    <div>Contact Information: {{ $getCustomer['spcus_cnumber'] }}</div>
                </td>
            </table>
            <table style="width: 100%;">
                <td style="width: 50%;">
                    <div>Address: {{ $getCustomer['spcus_address'] }}</div>
                </td>
            </table>
            <table style="width: 100%;">
                <td style="width: 50%;">
                    <div>Date Range: {{ $data['dateRange'] }}</div>
                </td>
            </table>
        </div>
    </div>
    <li style="margin-top:30px">SUMMARY OF REQUEST</li>
    <table style="width: 100%; border: 1px solid black; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="border: 1px solid black; padding: 2px;">Description</th>
                <th style="border: 1px solid black; padding: 2px;">Denomination</th>
                <th style="border: 1px solid black; padding: 2px;">Amount</th>
                <th style="border: 1px solid black; padding: 2px;">Count</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($getDenoms as $key => $value): ?>
                <tr>
                    <td style="border: 1px solid black; text-align: center; padding: 2px;">Gift Check Issued</td>
                    <td style="border: 1px solid black; text-align: center; padding: 2px;">{{$value['spexgcemp_denom']}}</td>
                    <td style="border: 1px solid black; text-align: center; padding: 2px;">{{$value['amount']}}</td>
                    <td style="border: 1px solid black; text-align: center; padding: 2px;">{{$value['denom_count']}}</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <li style="margin-top:30px">Issued Gift Checks</li>
    <table style="width: 100%; border: 1px solid black; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="border: 1px solid black; padding: 2px;">Date Issued</th>
                <th style="border: 1px solid black; padding: 2px;">Denomination</th>
                <th style="border: 1px solid black; padding: 2px;">Barcode Range</th>
                <th style="border: 1px solid black; padding: 2px;">Amount</th>
                <th style="border: 1px solid black; padding: 2px;">Validity</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($getDenoms as $key => $value): ?>
                <tr>
                    <td style="border: 1px solid black; text-align: center; padding: 2px;">{{ $value['Dateissued'] }}</td>
                    <td style="border: 1px solid black; text-align: center; padding: 2px;">{{$value['spexgcemp_denom']}}</td>
                    <td style="border: 1px solid black; text-align: center; padding: 2px;">{{ $value['barcodeMin']. '-' .$value['barcodeMax'] }}</td>
                    <td style="border: 1px solid black; text-align: center; padding: 2px;">{{ $value['denom_sum'] }}</td>
                    <td style="border: 1px solid black; text-align: center; padding: 2px;">{{ $data['dateRange'] }}</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <li style="margin-top: 30px;">NOTES & REMARKS</li>
    <div>A. All gift checks issued are subject to company policies</div>
    <div>B. All gift checks passes the date of validity cannot be redeemed</div>
    <div>C. For any discrepancies, please contact(1020, Treasury Dept.)</div>

    <table style="width: 100%; margin-top: 30px; border-spacing: 10px;">
        <thead>
            <tr>
                <td style="text-align: left; width: 33.33%;">
                    Prepared By:
                </td>
                <td style="text-align: left; width: 33.33%;">
                    Checked By:
                </td>
                <td style="text-align: left; width: 33.33%;">
                    Approved By:
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width: 33.33%;">
                    <p style="text-align: center; margin: 0;">
                        {{ strtoupper($data['prepBy']) }}
                    </p>
                    <hr style="margin-top: 0; margin-bottom: 0;">
                </td>
                <td style="width: 33.33%;">
                    <p style="text-align: center; margin: 0;">
                        {{ strtoupper($data['checkedBy']) }}
                    </p>
                    <hr style="margin-top: 0; margin-bottom: 0;">
                </td>
                <td style="width: 33.33%;">
                    <p style="text-align: center; margin: 0;">
                        {{ strtoupper($data['approvedBy']) }}
                    </p>
                    <hr style="margin-top: 0; margin-bottom: 0;">
                </td>
            </tr>
        </tbody>
    </table>

</body>

</html>