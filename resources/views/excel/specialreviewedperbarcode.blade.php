<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th style="border: 1px solid black; font-weight: bold">TRANSACTION DATE</th>
                <th style="border: 1px solid black; font-weight: bold">BARCODE</th>
                <th style="border: 1px solid black; font-weight: bold">DENOMINATION</th>
                <th style="border: 1px solid black; font-weight: bold">CUSTOMER</th>
                <th style="border: 1px solid black; font-weight: bold">APPROVAL #</th>
                <th style="border: 1px solid black; font-weight: bold">DATE APPROVED</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td style="border: 1px solid black; font-size: 10px">{{ $item->transdate }}</td>
                    <td style="border: 1px solid black; font-size: 10px">{{ $item->spexgcemp_barcode }}</td>
                    <td style="border: 1px solid black; font-size: 10px">{{ $item->denom }}</td>
                    <td style="border: 1px solid black; font-size: 10px">{{ $item->custname }}</td>
                    <td style="border: 1px solid black; font-size: 10px">{{ $item->num }}</td>
                    <td style="border: 1px solid black; font-size: 10px">{{ $item->dateApproved }}</td>

                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
