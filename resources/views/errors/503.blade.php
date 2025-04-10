<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Under Maintenance | Gift Check</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6c5ce7;
            --secondary: #a29bfe;
            --dark: #2d3436;
            --light: #f5f6fa;
            --accent: #fd79a8;
            --success: #00b894;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: var(--dark);
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            min-height: 100vh;
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            max-width: 1000px;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
        }

        .maintenance-image {
            width: 100%;
            max-width: 400px;
            margin: 0 auto 30px;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--dark);
            font-weight: 700;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p {
            font-size: 1.2rem;
            margin: 1rem 0;
            color: #555;
            font-weight: 400;
        }

        .note {
            margin: 2rem 0;
            padding: 15px;
            background-color: rgba(162, 155, 254, 0.1);
            border-left: 4px solid var(--primary);
            border-radius: 0 8px 8px 0;
            text-align: left;
        }

        .contact-info {
            margin-top: 2rem;
            padding: 20px;
            background-color: var(--light);
            border-radius: 10px;
        }

        .contact-info strong {
            color: var(--primary);
            font-weight: 600;
        }

        .progress-container {
            margin: 2rem 0;
            height: 8px;
            background-color: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
        }

    </style>
</head>

<body>
    <div class="container">
        <img src="/images/maintenance.jpg" alt="Under Maintenance" class="maintenance-image">

        <h1>We're Performing Scheduled Maintenance</h1>
        <p>Gift Check is currently undergoing essential updates and will be back shortly.</p>

        {{-- <div class="progress-container">
            <div class="progress-bar"></div>
        </div> --}}

        <div class="countdown">Back in approximately 5-10 minutes</div>

        <div class="note">
            Please check back soon. We're working hard to complete the maintenance as quickly as possible.
        </div>

        <div class="contact-info">
            <p>For further assistance, please contact our System Administrator - Corp IT at extensions
            <strong>1844</strong> or <strong>1953</strong>, or reach out to
            <strong>Norien</strong> or <strong>Claire</strong> directly.</p>
        </div>
    </div>
</body>

</html>
