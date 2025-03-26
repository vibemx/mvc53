<?php

require_once('../config/config.php');
require_once('../app/helpers/jsonHelper.php');
require_once('../app/helpers/textHelper.php');
require_once('../app/helpers/ApiResponse.php');
require_once('../core/View.php');
require_once('../core/Model.php');
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
            $this->handleModelError("El archivo del modelo '{$model}' no existe.");
        }

        require_once($modelFile);

        // Verificar si la clase del modelo existe
        if (!class_exists($model)) {
            $this->handleModelError("La clase del modelo '{$model}' no fue encontrada.");
        }

        try {
            // Instanciar el modelo
            $modelInstance = new $model();
            return $modelInstance;
        } catch (Exception $e) {
            // Manejar cualquier error al cargar o conectar con el modelo
            //$this->handleModelError($e->getMessage());
            $this->handleModelError('Lo sentimos, no hemos podido establecer conexión con la base de datos. Por favor, inténtalo más tarde. Si el problema persiste, contacta a soporte técnico.');
        }

        return null; // Si falla, devuelve null como fallback
    }


    // Manejo de errores al cargar el modelo
    private function handleModelError($errorMessage)
    {
        global $isApi; // Variable global para identificar si es API
        if ($isApi) {
            ApiResponse::error($errorMessage);
        } else {
            // Mostrar mensaje de error (personaliza tu vista de error aquí)
            $data = array(
                'code' => 400,
                'message' => $errorMessage
            );
            $this->loadView('errors/error', $data);
        }
        exit(); // Terminar la ejecución
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
            include($viewFile);
            include_once('../app/views/templates/footer.php');
        }
    }
}
