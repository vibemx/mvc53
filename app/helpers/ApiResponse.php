<?php

class ApiResponse
{
    /**
     * Genera una respuesta exitosa en formato JSON.
     *
     * @param string $message Mensaje descriptivo de la operación. Por defecto es "Operación exitosa.".
     * @param int $code Código de estado HTTP. Por defecto es 200 (OK).
     * @param mixed $data Los datos que se enviarán en la respuesta. Puede ser un array, objeto o null.
     * @return string Respuesta JSON con la estructura definida.
     */
    public static function success($message = 'Operación exitosa.', $code = 200, $data = null)
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode(array(
            'success' => true,       // Indica que la operación fue exitosa.
            'status' => $code,       // Código de estado HTTP.
            'message' => $message,   // Mensaje descriptivo de la operación.
            'payload' => array(      // Encapsula los datos y posibles errores.
                'data' => $data,     // Información solicitada o generada por la operación.
            )
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Genera una respuesta de error en formato JSON.
     *
     * @param mixed $errors Los errores que se enviarán en la respuesta. Puede ser un array, cadena o null.
     * @param string $message Mensaje descriptivo del error. Por defecto es "Error en la operación.".
     * @param int $code Código de estado HTTP. Por defecto es 400 (Bad Request).
     * @return string Respuesta JSON con la estructura definida.
     */
    public static function error($message = 'Error en la operación.', $code = 400, $errors = null)
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode(array(
            'success' => false,      // Indica que la operación falló.
            'status' => $code,       // Código de estado HTTP.
            'message' => $message,   // Mensaje descriptivo del error.
            'payload' => array(      // Encapsula los datos y posibles errores.
                'errors' => $errors?:$message  // Lista de errores o mensajes de error.
            )
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}
?>