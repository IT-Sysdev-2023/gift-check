<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <table>
        <thead>
            <tr>
                <th>Barcode #</th>
                <th>Denom</th>
                <th>GC Type</th>
                <th>Customer</th>
                <th>Status</th>
                <th>Balance</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $item['vs_barcode'] }}</td>
                    <td>{{ $item['vs_tf_denomination'] }}</td>
                    <td>
                        @if($item['vs_gctype'] == '1')
                            Regular
                        @elseif($item['vs_gctype'] == '2')
                            Promo
                        @elseif($item['vs_gctype'] == '3')
                            Special External
                        @elseif($item['vs_gctype'] == '6')
                            Beam and Go
                        @endif
                    </td>
                    <td>{{$item['customer']}}</td>
                    <td>{{$item['vs_tf_used'] == '*' ? 'Used' : ''}}</td>
                    <td>{{$item['vs_tf_balance']}}</td>
                    <td>{{ \Carbon\Carbon::parse($item['vs_date'])->toDateString() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>