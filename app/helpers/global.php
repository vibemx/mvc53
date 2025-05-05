<?php

class utils
{
    public static function set_http_response_code($code) {
        $status_codes = array(
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            301 => 'Moved Permanently',
            302 => 'Found',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            // Agrega más códigos si los necesitas
        );
    
        $text = isset($status_codes[$code]) ? $status_codes[$code] : 'Unknown Status';
        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        header("$protocol $code $text");
    }
}
