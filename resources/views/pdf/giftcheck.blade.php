<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production Request Form</title>
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
        <h4 class="subtitle">Production Request Form</h4>
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
                    <td><strong>Budget</strong></td>
                    <td>{{$data['budget']}}</td>
                </tr>
                <tr>
                    <td><strong>Remarks</strong></td>
                    <td>{{$data['remarks']}}</td>
                </tr>
            </table>
        </section>
        
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

        <section class="signatures clearfix">
            <p>Prepared by:</p>
            <div class="signature-block">
                <h3 style="text-decoration: underline;">{{ $data['preparedBy']}}</h3>
                <p>Sr Cash Clerk</p>
            </div>
        </section>
        

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
    background-color: #f5f5f5;
}

.container {
    width: 800px;
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
.company-logo {
    text-align: right; /* Aligns the content to the right */
}
.company-logo img {
    padding-right: 20px;
    width: 100px;
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
    text-align: center;
    font-size: 12px;
    color: #666;
    margin-top: 40px;
}

</style>
</html>