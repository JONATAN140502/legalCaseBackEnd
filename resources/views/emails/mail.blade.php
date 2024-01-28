<!DOCTYPE html>
<html>
<head>
    <title>Correo de prueba</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        p {
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>¡Hola, {{ $nombre }}!</h1>
        <h3>{{ $asunto }}</h3>
        <p>{{ $descripcion1 }}</p>
        <p>{{ $descripcion2 }}</p>
        <p>{{ $descripcion3 }}</p>
    </div>
</body>
</html>

