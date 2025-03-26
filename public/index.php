<?php
require_once('../config/config.php');
require_once('../core/Router.php');

// Crear una instancia del Router
$router = new Router();

// Manejar la solicitud entrante
$router->handleRequest();
