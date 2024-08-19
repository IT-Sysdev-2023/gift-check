<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Example</title>
</head>
<body>
    <h1>Barcode Example</h1>
    <div>
        {!! DNS1D::getBarcodeHTML('123456789', 'C128') !!}
    </div>
</body>
</html>
