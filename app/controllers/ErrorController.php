<?php

class ErrorController extends Controller
{
    /**
     * Método para manejar la solicitud de un Errors.
     * @param string $base64Id El ID del Errors en formato base64.
     */
    public function error_404()
    {
        $this->loadView('errors/404');
    }
    public function error($code = 400, $message = '¡Ups! Ocurrío un error inesperado.')
    {
        $data = array(
            'code' => $code,
            'message' => $message
        );
        $this->loadView('errors/error', $data);
    }
}
