<?php

function limpiarEspacios($txt)
{
    return preg_replace('/\s+/', ' ', trim($txt));
}

function limpiarAcentos($txt, $n = true)
{
    $reemplazos = array(
        'á' => 'a',
        'à' => 'a',
        'ä' => 'a',
        'â' => 'a',
        'Á' => 'A',
        'À' => 'A',
        'Ä' => 'A',
        'Â' => 'A',
        'é' => 'e',
        'è' => 'e',
        'ë' => 'e',
        'ê' => 'e',
        'É' => 'E',
        'È' => 'E',
        'Ë' => 'E',
        'Ê' => 'E',
        'í' => 'i',
        'ì' => 'i',
        'ï' => 'i',
        'î' => 'i',
        'Í' => 'I',
        'Ì' => 'I',
        'Ï' => 'I',
        'Î' => 'I',
        'ó' => 'o',
        'ò' => 'o',
        'ö' => 'o',
        'ô' => 'o',
        'Ó' => 'O',
        'Ò' => 'O',
        'Ö' => 'O',
        'Ô' => 'O',
        'ú' => 'u',
        'ù' => 'u',
        'ü' => 'u',
        'û' => 'u',
        'Ú' => 'U',
        'Ù' => 'U',
        'Û' => 'U',
        'Ü' => 'U',
        'Ñ' => 'ñ',
        'ç' => 'c',
        'Ç' => 'C'
    );
    if ($n) {
        $reemplazos['ñ'] = 'n';
        $reemplazos['Ñ'] = 'N';
    }
    return strtr($txt, $reemplazos);
}
/**
 * Limpia un texto según un modo y un formato especificados.
 *
 * @param string $txt El texto a limpiar.
 * @param string $modo El tipo de limpieza a aplicar. Opciones:
 * - 'basico': Valida caracteres permitidos y evita código.
 * - 'sin_acentos': Elimina acentos.
 * - 'sin_espacios': Elimina todos los espacios.
 * - 'alfanum': Deja solo letras y números.
 * - 'slug': Genera un slug con palabras separadas por guiones.
 * - 'numeros': Deja solo números.
 * - 'json': Escapa caracteres para JSON.
 * - 'default': Limpieza básica de acentos y caracteres especiales.
 * @param string $fmt El formato opcional a aplicar (e.g., 'mayus', 'minus', 'titulo', 'camel').
 * @return string El texto limpio y transformado.
 */
function limpiarTexto($txt, $modo = 'default', $fmt = 'default')
{
    $txt = trim($txt);

    $modos = array(
        'basico' => function ($str) {
            return preg_match('/[<>{}]/', $str)
                ? limpiarEspacios(preg_replace('/[^a-zA-Z0-9\s]/u', '', limpiarAcentos($str,false)))
                : limpiarEspacios(limpiarAcentos($str,false));
        },
        'basico_espaciado' => function ($str) {
            return preg_match('/[<>{}]/', $str)
                ? preg_replace('/[^a-zA-Z0-9\s]/u', '', limpiarAcentos($str,false))
                : limpiarAcentos($str,false);
        },
        'sin_acentos' => 'limpiarAcentos',
        'sin_espacios' => function ($str) {
            return preg_replace('/\s+/', '', $str);
        },
        'alfanum' => function ($str) {
            return preg_replace('/[^a-zA-Z0-9]/', '', $str);
        },
        'slug' => function ($str) {
            $str = limpiarAcentos($str);
            $str = strtolower($str);
            $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
            return preg_replace('/[\s-]+/', '-', trim($str, '-'));
        },
        'json' => 'addslashes',
        'numeros' => function ($str) {
            return preg_replace('/[^0-9.-]/', '', $str);
        },
        'default' => function ($str) {
            $str = limpiarAcentos($str);
            return preg_replace('/[^a-zA-Z0-9\sñÑ.,]/u', ' ', $str);
        }
    );

    $txt = isset($modos[$modo]) ? $modos[$modo]($txt) : $modos['default']($txt);

    return aplicarFormato($txt, $fmt);
}
function aplicarFormato($txt, $tipo)
{
    switch ($tipo) {
        case 'mayus':
            return strtoupper($txt);
        case 'minus':
            return strtolower($txt);
        case 'titulo':
            return ucwords(strtolower($txt));
        case 'camel':
            return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($txt)))));
        default:
            return $txt;
    }
}
