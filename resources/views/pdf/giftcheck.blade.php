<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$data['subtitle']}}</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <header>
            <div class="company-logo">
                <img src="{{ public_path('storage/assets/sysdev.png') }}" alt="Sysdev Logo">
            </div>
            <div class="title">
                <h1>Gift Check Monitoring System</h1>

            </div>
        </header>
        <h4 class="subtitle">{{$data['subtitle']}}</h4>
        <section class="form-info">
            <table>
                <tr>
                    <td><strong>PR No.</strong></td>
                    <td>{{$data['pr']}}</td>
                </tr>
                <tr>
                    <td><strong>Date Requested</strong></td>
                    <td>{{$data['dateRequested']}}</td>
                </tr>
                <tr>
                    <td><strong>Date Needed</strong></td>
                    <td>{{$data['dateNeeded']}}</td>
                </tr>
                <tr>
                    <td><strong>Current Budget</strong></td>
                    <td>{{$data['budget']}}</td>
                </tr>
                <tr>
                    <td><strong>Remarks</strong></td>
                    <td>{{$data['remarks']}}</td>
                </tr>
                @if(!empty($data['budgetRequested']))
                <tr>
                    <td><strong>Budget Requested</strong></td>
                    <td>{{$data['budgetRequested']}}</td>
                </tr>
                @endif
            </table>
        </section>

        @if(!empty($data['barcode']))
        <section class="table-section">
            <table class="denomination-table">
                <thead>
                    <tr>
                        <th>Denomination</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['barcode'] as $denom)
                    <tr>

                        <td>Php {{$denom['denomination']}}</td>
                        <td>{{$denom['qty']}}</td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
        @endif

    

        <table class="signature-table">
            <tr>
                @foreach($data['signatures'] as $title => $name)
                <td>
                    <p class="signature-label">{{ Str::headline($title) }}:</p>
                    <p style="text-decoration: underline;"><strong>{{ $name['name'] }}</strong></p>
                    <div class="signature-line">{{$name['position']}}</div>
                </td>
                @endforeach
            </tr>
        </table>


        <footer>
            <p>Alturas Group of Companies</p>
            <p>Dampas Dist, Tagbilaran City, Bohol, Philippines</p>
            <p>Tel: (038) 501-6284 Operator: (038) 501-3000 Local: 1800/1847</p>
        </footer>
    </div>
</body>
<style>

body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: white;
    position: relative;
    min-height: 100%;
    padding-bottom: 100px; /* Adjust this to the height of the footer */
    box-sizing: border-box;
}

.container {
    width: 100%;
    max-width: 870px; /* Adjust this to fit better within the A4 dimensions */
    margin: 20px auto;
    background-color: white;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

header {
    text-align: center;
    margin-bottom: 20px;
}
.subtitle{
    padding-top: 20px;
    text-transform: uppercase;
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
    .signatures {
        width: 100%;
        margin-top: 50px;
    }
    .signature-label{
        text-align: left;
        padding-bottom: 15px
    }
.company-logo {
    text-align: right; /* Aligns the content to the right */
}
.company-logo img {
    padding-right: 20px;
    max-width: 100px;
    width: 100%; /* Ensure it scales properly */
    height: auto;
    margin-bottom: 10px;
}

.title h1 {
    text-transform: uppercase;
    font-size: 24px;
    margin-bottom: 5px;
}

.title h2 {
    font-size: 20px;
    margin-bottom: 20px;
}

.form-info table {
    width: 100%;
    margin-bottom: 20px;
    border-collapse: collapse;
}

.form-info td {
    padding: 8px;
    border: 1px solid #ccc;
}

.form-info td:first-child {
    font-weight: bold;
}

.table-section {
    margin-bottom: 20px;
}

.denomination-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.denomination-table th, .denomination-table td {
    border: 1px solid #ccc;
    padding: 10px;
    text-align: center;
}

.denomination-table th {
    background-color: #f0f0f0;
}

.signatures {
    margin-top: 20px;
}

.signature-block {
    float: left;
    width: 200px; /* Adjust based on your design */
    text-align: center;
    margin-left: 30px;
}

.clearfix::after {
    content: "";
    display: table;
    clear: both;
}

footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 100px; /* Adjust based on your footer content */
    text-align: center;
    font-size: 15px; /* Adjust the font size to fit */
    color: #666;
    background-color: white;
    padding: 10px;
    box-sizing: border-box;
}

</style>
</html>
