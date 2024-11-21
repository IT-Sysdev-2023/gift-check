<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Purchased Gc</title>
</head>

<body>
    <table>
        <thead>
            <tr>
                <th style="border: 1px solid black;">DATE PURCHASED</th>
                <th style="border: 1px solid black;">BARCODE</th>
                <th style="border: 1px solid black;">DENOMINATION</th>
                <th style="border: 1px solid black;">AMOUNT REDEEM</th>
                <th style="border: 1px solid black;">BALANCE</th>
                <th style="border: 1px solid black;">CUSTOMER NAME</th>
                <th style="border: 1px solid black;">STORE PURCHASED</th>
                <th style="border: 1px solid black;">TRANSACTION #</th>
                <th style="border: 1px solid black;">STORE REDEEM</th>
                <th style="border: 1px solid black;">TERMINAL #</th>
                <th style="border: 1px solid black;">VALIDATION</th>
                <th style="border: 1px solid black;">GC TYPE</th>
                <th style="border: 1px solid black;">GC DATE VERIFIED</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td style="border: 1px solid black; font-size: 10px">{{ $item->trans_datetime }}</td>
                    <td style="border: 1px solid black; font-size: 10px">{{ $item->barcode }}</td>
                    <td style="border: 1px solid black; font-size: 10px">{{ $item->denomination }}</td>
                    <td style="border: 1px solid black; font-size: 10px">{{ $item->purchasecred }}</td>
                    <td style="border: 1px solid black; font-size: 10px">{{ $item->balance }}</td>
                    <td style="border: 1px solid black; font-size: 10px">{{ $item->fullname }}</td>
                    <td style="border: 1px solid black; font-size: 10px">{{ $item->storepurchased }}</td>
                    <td style="border: 1px solid black; font-size: 10px">{{ $item->trans_number }}</td>
                    <td style="border: 1px solid black; font-size: 10px">{{ $item->busnessunited }}</td>
                    <td style="border: 1px solid black; font-size: 10px">{{ $item->terminalno }}</td>
                    <td style="border: 1px solid black; font-size: 10px">{{ $item->valid_type }}</td>
                    <td style="border: 1px solid black; font-size: 10px">{{ $item->gc_type }}</td>
                    <td style="border: 1px solid black; font-size: 10px">{{ $item->vsdate }}:{{ $item->vstime }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
