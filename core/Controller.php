<?php
require_once('../config/config.php');
require_once('../core/View.php');
require_once('../core/Model.php');
error_reporting(E_ALL & ~E_WARNING);
ini_set('display_errors', 'on');
class Controller
{
    // Función para cargar el modelo
    public function baseURL($dir = '')
    {
        return URL_BASE . $dir;
    }
    public function loadModel($model)
    {
        // Verifica si $model termina con "Model"
        if (substr($model, -5) !== 'Model') {
            $model .= 'Model';
        }

        // Ruta al archivo del modelo
        $modelFile = "../app/models/{$model}.php";

        // Verificar si el archivo del modelo existe
        if (!file_exists($modelFile)) {
            return false;
            // $this->handleModelError("El archivo del modelo '{$model}' no existe.");
        }

        require_once($modelFile);

        // Verificar si la clase del modelo existe
        if (!class_exists($model)) {
            //$this->handleModelError("La clase del modelo '{$model}' no fue encontrada.");
            throw new Exception("La clase del modelo '{$model}' no fue encontrada.");
        }

        try {
            // Instanciar el modelo
            $modelInstance = new $model();
            return $modelInstance;
        } catch (Exception $e) {
            // Manejar cualquier error al cargar o conectar con el modelo
            //$this->handleModelError($e->getMessage());
            throw new Exception('Lo sentimos, no hemos podido establecer conexión con la base de datos. Por favor, inténtalo más tarde. Si el problema persiste, contacta a soporte técnico.');
        }

        return null; // Si falla, devuelve null como fallback
    }

    // Función para cargar la vista
    public function loadView($view, $data = null)
    {
        $viewFile = "../app/views/{$view}.php";
        if (file_exists($viewFile)) {
            // Extraer variables si $data no es null
            if (is_array($data)) {
                extract($data);
            }
            include_once('../app/views/templates/header.php');
            include_once($viewFile);
            include_once('../app/views/templates/footer.php');
        }
    }
}
