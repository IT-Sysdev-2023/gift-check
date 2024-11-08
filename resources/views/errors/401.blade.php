{{-- resources/views/fullscreen_image.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Full-Screen Image</title>
    <style>
        /* Reset body and html margins */
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        /* Container for the full-screen image */
        .fullscreen-image {
            background-image: url('{{ asset('images/errors/401.png') }}'); /* Replace with your image path */
            background-size: cover;     /* Ensures image covers the screen */
            background-position: center; /* Centers the image */
            background-repeat: no-repeat;
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2em;
        }
    </style>
</head>
<body>
    <div class="fullscreen-image">
    </div>
</body>
</html>
