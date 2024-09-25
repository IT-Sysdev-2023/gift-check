<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .container {
            margin: 50px;
        }

        .content {
            margin-top: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            /* Ensure equal width for all columns */
        }

        .table th,
        .table td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
            word-wrap: break-word;
            /* Ensure text wraps within cells */
        }

        .table th {
            background-color: #ffffff;
        }

        .denom {
            margin-top: 30px;
        }

        .denom table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            /* Ensure equal width for all columns */
        }

        .denom th,
        .denom td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
            word-wrap: break-word;
            /* Ensure text wraps within cells */
        }

        .denom th {
            background-color: #f0f0f0;
        }

        .prepby {
            position: absolute;
            top: 500;
            width: 300px;
            height: 100px;
        }

        .reviewedBy {
            position: absolute;
            top: 600;
            width: 300px;
            height: 100px;
        }

        .approvedBy {
            position: absolute;
            right: 20px;
            top: 600;
            width: 300px;
            height: 100px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            GIFT CHECK MONITORING SYSTEM
        </div>
        <div class="content">
            <div style="text-align: left; font-weight: bold; margin-bottom: 10px;">
                Production Request Form
            </div>
            <table class="table">
                <tr>
                    <th>PR No.</th>
                    <td>{{$data['pr_no']}}</td>
                </tr>
                <tr>
                    <th>Date Requested</th>
                    <td>{{$data['dateRequested']}}</td>
                </tr>
                <tr>
                    <th>Budget</th>
                    <td>{{$data['currentBudget']}}</td>
                </tr>
                <tr>
                    <th>Remarks</th>
                    <td>{{$data['Remarks']}}</td>
                </tr>
            </table>
            <div class="denom">
                <table>
                    <thead>
                        <tr>
                            <th>Denomination</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($barcodes as $item)
                            <tr>
                                <td>{{$item['denomination']}}</td>
                                <td>{{$item['pe_items_quantity']}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="prepby">
                <div style="text-align: left">
                    Prepared By:
                </div>
                <div style="text-align: center; margin-top: 10px">
                    {{strtoupper($data['preparedBy'])}}
                </div>
                <hr style="border: 0.5px solid #000; width: 100%; text-align: center;">
                <div>
                    <div style="text-align: center">
                        <small>Sr. Cash Clerk</small>
                    </div>
                </div>
            </div>
            <div class="reviewedBy">
                <div style="text-align: left">
                    Reviewed By:
                </div>
                <div style="text-align: center; margin-top: 10px">
                    {{strtoupper($data['checkby'])}}
                </div>
                <hr style="border: 0.5px solid #000; width: 100%; text-align: center;">
                <div>
                    <div style="text-align: center">
                        <small>Sr. Supervisor</small>
                    </div>
                </div>
            </div>
            <div class="approvedBy">
                <div style="text-align: left">
                    Approved By:
                </div>
                <div style="text-align: center; margin-top: 10px">
                    {{($data['approvedBy'])}}
                </div>
                <hr style="border: 0.5px solid #000; width: 100%; text-align: center;">
                <div>
                    <div style="text-align: center">
                        <small>Marketing Manager</small>
                    </div>
                </div>
            </div>
            <div style="position:absolute; bottom:0px; width:90%;">
                <div style="text-align:center">
                    <small>Alturas Group of Companies</small>
                </div>
                <div style="text-align:center">
                    <small>Dampas Dist. Tagbilaran City, Bohol</small>
                </div>
                <div style="text-align:center">
                    <small>Tel # (038)501-6284: (038)501-3000 Local: 1800/1847</small>
                </div>
            </div>
        </div>
    </div>
</body>

</html>