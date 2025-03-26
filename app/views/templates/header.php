<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=NAME_SYSTEM?></title>
    <!-- Bootstrap 5 -->
    <base href="<?= $this->baseURL(); ?>">
    <script>
        base_url = '<?= URL_BASE; ?>';

        function baseURL(dir = 'home/index') {
            return base_url + dir;
        }
    </script>
    <link rel="shortcut icon" href="https://betarcode.com/assets/img/favicon/favicon.png<? /*$this->baseURL('assets/img/logo.webp');*/ ?>" type="image/x-icon">
    <link href="<?= $this->baseURL('assets/bootstrap/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?= $this->baseURL('assets/fontawesome-free/css/all.min.css'); ?>" rel="stylesheet">
    <link href="<?= $this->baseURL('assets/main.css'); ?>" rel="stylesheet">
    <script src="<?= $this->baseURL('assets/main.js'); ?>"></script>
</head>

<body>
    <!-- Marca de agua con texto -->
    <div class="watermark">
        <p>USUARIO NAMEE</p> <!-- Aquí se coloca el texto de la sesión -->
        <p>USUARIO NAMEE</p> <!-- Aquí se coloca el texto de la sesión -->
        <p>USUARIO NAMEE</p> <!-- Aquí se coloca el texto de la sesión -->
        <p>USUARIO NAMEE</p> <!-- Aquí se coloca el texto de la sesión -->
    </div>
    <div style="width: 90%; margin: auto;">
        <header>
            <!-- Header con imagen -->
            <div style="width: 100%; height: 60px; background: #eeeeee;">
                <div class="text-center">
                    <img style="height: 60px;" src="https://source.unsplash.com/random/60x60" alt="Encabezado">
                </div>
            </div>

            <!-- Menu principal con iframe -->
            <div style="width: 100%;" class="menu_iconos">
                <!-- Barra de navegación -->
                <nav class="navbar navbar-expand-lg navbar-light bg-light" style="margin-top: -30px; margin-bottom: 20px;">
                    <div class="container-fluid">
                        <a class="navbar-brand" href=""><?=NAME_SYSTEM?></a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <!-- Nombre de usuario -->
                            <ul class="navbar-nav mb-2 mb-lg-0 ms-auto order-lg-last">
                                <li class="nav-item">
                                    <span class="nav-link"><?= 'USUARIO NOMBRE';?></span>
                                </li>
                            </ul>
                            <!-- Menú principal -->
                            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Menú
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#">Opcion 1</a></li>
                                        <li><a class="dropdown-item" href="#">Opcion 2</a></li>
                                    </ul>
                                </li>
                            </ul>

                        </div>
                    </div>
                </nav>

        </header>