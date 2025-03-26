<?php
require_once('../config/config.php');
require_once('../app/helpers/ApiResponse.php');
require_once('../core/Route.php');

require_once('../core/Controller.php');
class Router extends Controller
{
    // Almacena las rutas definidas
    private $routes = [];

    public function __construct()
    {
        // Cargar las rutas definidas en routes/web.php
        self::loadRoutes();
        $master = new Controller();
    }

    // Cargar las rutas desde el archivo de rutas
    private function loadRoutes()
    {
        require_once('../routes/routes.php');  // Asegúrate de que la ruta es correctar
    }

    // Manejar la solicitud entrante
    public function handleRequest()
    {
        
        $request_method = $_SERVER['REQUEST_METHOD'];  // Método de la solicitud (GET, POST)
        $uri = isset($_GET['uri']) ? $_GET['uri'] : 'home/index'; // URL solicitada, por defecto 'home/index'
        if (strpos($uri, 'assets') === 0) {
            return;
        }
        $route = Route::getRoute($request_method, $uri);

        if ($route) {
            $controller = $route['controller'];
            $method = $route['method'];
            $params = $route['params'];

            // Determinar la ruta del archivo del controlador
            $controllerFile = self::getControllerPath($route);
            $controllerClass = ucfirst($controller);

            if (file_exists($controllerFile)) {
                require_once($controllerFile);
                $modelo = str_replace('Controller', '', $controllerClass);
                Controller::loadModel($modelo);
                
                $controllerObject = new $controllerClass();
                self::invokeMethod($controllerObject, $method, $params);
            } else {
                self::handleError(404, "El controlador '{$controllerClass}' no fue encontrado.", $route['type']);
            }
        } else {
            self::handleError(404, "La ruta '{$uri}' no fue encontrada.", $route['type']);
        }
    }
    // Invocar el método del controlador
    private function invokeMethod($controllerObject, $method, $params)
    {
        if (method_exists($controllerObject, $method)) {
            call_user_func_array(array($controllerObject, $method), $params);  // Llamar a la acción
        } else {
            self::handleError(404, "La acción '{$method}' no fue encontrada.", $controllerObject->getType());
        }
    }
    // Manejar errores
    private function handleError($code, $message, $type)
    {
        // Si es una API, responde con JSON de error
        if (self::isApi($type)) {
            header('Content-Type: application/json');
            echo json_encode(array('status' => 'error', 'message' => $message, 'code' => $code));
        } else {
            // Si no es una API, muestra la vista de error
            include "../app/views/errors/404.php";
        }
        exit;
    }
    // Verificar si la solicitud es API
    private function isApi($type)
    {
        $segments = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        return ($type === 'api' || $segments[1] === 'api');
    }

    // Obtener la ruta del controlador
    private function getControllerPath($route)
    {
        $controllerPath = "../app/controllers/";
        $controllerPath .= self::isApi($route['type']) ? "api/{$route['controller']}.php" : "{$route['controller']}.php";
        return $controllerPath;
    }
}
