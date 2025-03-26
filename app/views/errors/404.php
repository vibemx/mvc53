<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error 404 - Página no encontrada</title>
    <base href="<?= URL_BASE;?>">
    <!-- Bootstrap CSS -->
    <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
        }
        .error-container {
            text-align: center;
            max-width: 600px;
        }
        .error-title {
            font-size: 6rem;
            font-weight: bold;
            color: #ff6b6b;
        }
        .error-text {
            font-size: 1.25rem;
            margin-top: 1rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-title">404</div>
        <p class="error-text">¡Ups! La página que buscas no se encontró.</p>
        <p class="error-text">URL: <span class="text-danger" id="error-url"></span></p>
        <a href="/" class="btn btn-primary mt-4">Volver a la página principal</a>
    </div>

    <script>
        // Display the current URL in the error message
        document.getElementById('error-url').textContent = window.location.href;
    </script>

    <!-- Bootstrap JS -->
    <script src="assets/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>