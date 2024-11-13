<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GC Sales Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
        }

        .page-break {
            page-break-after: always;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            text-align: center;
        }

        .header {
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 20px;
            text-transform: uppercase;
            margin: 0;
        }

        .header h2 {
            font-size: 16px;
            margin: 5px 0;
        }

        .report-dates {
            font-size: 12px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        td.left {
            text-align: left;
        }

        .total {
            font-weight: bold;
        }
    </style>
</head>

<body>
    @foreach ($data['stores'] as $key => $store)
        <div class="container">
            <div class="header">
                @if (!empty($store['header']['store']))
                    <h2>{{$store['header']['store']}}</h2>
                @endif
                <h1>ALTURAS GROUP OF COMPANIES</h1>
                <h3>GC Sales Report</h3>
            </div>

            @if (empty($store['error']))

                <div class="report-dates">

                    @if (!empty($store['header']['transactionDate']))
                        <p>Transaction Date: {{$store['header']['transactionDate']}}</p>
                    @endif

                    <span>Report Type: </span>
                    @foreach ($store['header']['reportType'] as $item)
                        {{Str::headline($item)}} {{ !$loop->last ? ',' : '' }}
                    @endforeach
                    <p>Report Created: {{$store['header']['reportCreated']}}</p>
                </div>


                @if (!empty($store['data']['gcSales']))
                    <table>
                        <tr>
                            <th colspan="5">Cash Sales</th>
                        </tr>
                        <tr>
                            <th class="left">Gc Denomination</th>
                            <th>GC Sold</th>
                            <th>Sub Total</th>
                            <th>Line Disc.</th>
                            <th>Net</th>
                        </tr>

                        @foreach ($store['data']['gcSales']['cashSales'] as $item)
                            <tr>
                                <td class="left">{{$item->denomination}}</td>
                                <td>{{$item->cnt}}</td>
                                <td>{{$item->densum}}</td>
                                <td>{{$item->lineDiscount}}</td>
                                <td>{{$item->netIncome}}</td>
                            </tr>
                        @endforeach

                        <tr class="total">
                            <td colspan="4" class="left">Total Net:</td>
                            <td>{{$store['data']['gcSales']['totalCashSales']}}</td>
                        </tr>
                    </table>


                    <table>
                        <tr>
                            <th colspan="5">Card Sales</th>
                        </tr>
                        <tr>
                            <th class="left">GC Denomination</th>
                            <th>GC Sold</th>
                            <th>Sub Total</th>
                            <th>Line Disc.</th>
                            <th>Net</th>
                        </tr>
                        @foreach ($store['data']['gcSales']['cardSales'] as $item)
                            <tr>
                                <td class="left">{{$item->denomination}}</td>
                                <td>{{$item->cnt}}</td>
                                <td>{{$item->densum}}</td>
                                <td>{{$item->lineDiscount}}</td>
                                <td>{{$item->netIncome}}</td>
                            </tr>
                        @endforeach
                        <tr class="total">
                            <td colspan="4" class="left">Total Net:</td>
                            <td>{{$store['data']['gcSales']['totalCardSales']}}</td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <th colspan="5">AR</th>
                        </tr>
                        <tr>
                            <th class="left">GC Denomination</th>
                            <th>GC Sold</th>
                            <th>Sub Total</th>
                            <th>Line Disc.</th>
                            <th>Net</th>
                        </tr>
                        @foreach ($store['data']['gcSales']['ar'] as $item)
                            <tr>
                                <td class="left">{{$item->denomination}}</td>
                                <td>{{$item->cnt}}</td>
                                <td>{{$item->densum}}</td>
                                <td>{{$item->lineDiscount}}</td>
                                <td>{{$item->netIncome}}</td>
                            </tr>
                        @endforeach
                        <tr class="total">
                            <td colspan="4" class="left">Customer Total Discount:</td>
                            <td>{{$store['data']['gcSales']['totalCustomerDiscount']}}</td>
                        </tr>
                        <tr class="total">
                            <td colspan="4" class="left">Total Net:</td>
                            <td>{{$store['data']['gcSales']['totalAr']}}</td>
                        </tr>
                    </table>
                @endif

                @if (!empty($store['footer']['gcSalesFooter']))
                    <table>
                        <!-- Gc Sales Footer -->
                        @foreach($store['footer']['gcSalesFooter'] as $title => $name)
                            <tr class="total">
                                <td colspan="4" class="left">{{Str::headline($title)}}:</td>
                                <td>{{$name}}</td>
                            </tr>
                        @endforeach
                    </table>
                @endif
                @if (!empty($store['footer']['refundFooter']))
                    <table>
                        <!-- Refund Footer -->
                        @foreach($store['footer']['refundFooter'] as $title => $name)
                            <tr class="total">
                                <td colspan="4" class="left">{{Str::headline($title)}}:</td>
                                <td>{{$name}}</td>
                            </tr>
                        @endforeach
                    </table>
                @endif
                @if (!empty($store['footer']['revalidationFooter']))
                    <table>
                        <!-- Revalidation Footer -->
                        @foreach($store['footer']['revalidationFooter'] as $title => $name)
                            <tr class="total">
                                <td colspan="4" class="left">{{Str::headline($title)}}:</td>
                                <td>{{$name}}</td>
                            </tr>
                        @endforeach
                    </table>
                @endif
            @else
                <span>No Transactions</span>
            @endif
        </div>

        @if ($key < count($data['stores']) - 1)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>

</html>