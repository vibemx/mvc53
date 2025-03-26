<?php

class Route
{

    // Array para almacenar las rutas
    protected static $routes = [];

    // Método para registrar una ruta GET
    public static function get($uri, $action, $type = null)
    {
        self::$routes['GET'][$uri] = array(
            'action' => $action,
            'regex' => self::generateRegex($uri),
            'type' => $type
        );
    }

    // Método para registrar una ruta POST (si la necesitas)
    public static function post($uri, $action, $type = null)
    {
        self::$routes['POST'][$uri] = array(
            'action' => $action,
            'regex' => self::generateRegex($uri),
            'type' => $type
        );;
    }
    public static function put($uri, $action, $type = null)
    {
        self::$routes['PUT'][$uri] = array(
            'action' => $action,
            'regex' => self::generateRegex($uri),
            'type' => $type
        );
    }
    public static function delete($uri, $action, $type = null)
    {
        self::$routes['DELETE'][$uri] = array(
            'action' => $action,
            'regex' => self::generateRegex($uri),
            'type' => $type
        );;
    }

    // Método para registrar middleware (si es necesario)
    public static function middleware($middleware, $uri)
    {
        if (isset(self::$routes['GET'][$uri])) {
            self::$routes['GET'][$uri]['middleware'][] = $middleware;
        }
        if (isset(self::$routes['POST'][$uri])) {
            self::$routes['POST'][$uri]['middleware'][] = $middleware;
        }
    }

    // Método para obtener una ruta y sus datos
    public static function getRoute($method, $uri)
    {
        if (isset(self::$routes[$method])) {
            foreach (self::$routes[$method] as $routePattern => $routeData) {
                // Si la ruta tiene una expresión regular
                if (preg_match($routeData['regex'], $uri, $matches)) {
                    // Extraer parámetros dinámicos de la URI
                    array_shift($matches); // El primer elemento es la URI completa
                    $controllerAction = explode('@', $routeData['action']);
                    $controller = $controllerAction[0];
                    $action = $controllerAction[1];
                    return array('controller' => $controller, 'method' => $action, 'params' => $matches, 'type' => $routeData['type']);
                }
            }
        }
        return null; // No se encontró ninguna ruta
    }

    // Generar una expresión regular a partir de la URI con parámetros dinámicos
    private static function generateRegex($uri)
    {
        // Reemplazar los parámetros dinámicos {id} por una expresión regular que captura cualquier cosa
        return '#^' . preg_replace('/\{([a-zA-Z0-9_-]+)\}/', '([^/]+)', $uri) . '$#';
    }
}
