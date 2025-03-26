<?php

function getPost($postData)
{
    // Crear un objeto genérico (stdClass)
    $objeto = new stdClass();

    // Iterar sobre las claves del array recibido (como $_POST)
    foreach ($postData as $campo => $valor) {
        // Asignar dinámicamente las propiedades al objeto
        $objeto->$campo = $valor;
    }

    // Retornar el objeto con las propiedades asignadas
    return $objeto;
}
function getPostWithFiles($postData, $fileData)
{
    // Crear un objeto genérico (stdClass)
    $objeto = new stdClass();
    // Procesar los datos de archivos si existen
    foreach ($fileData as $campo => $file) {
        if (is_array($file['name'])) {
            // Si es un array de archivos (multiple input files)
            $objeto->$campo = array();
            foreach ($file['name'] as $index => $filename) {
                $objeto->{$campo}[] = array(
                    'name' => $filename,
                    'type' => $file['type'][$index],
                    'tmp_name' => $file['tmp_name'][$index],
                    'error' => $file['error'][$index],
                    'size' => $file['size'][$index],
                );
            }
        } else {
            // Si es un archivo único
            $objeto->$campo = array(
                'name' => $file['name'],
                'type' => $file['type'],
                'tmp_name' => $file['tmp_name'],
                'error' => $file['error'],
                'size' => $file['size']
            );
        }
    }

    // Retornar el objeto combinado
    return $objeto;
}
/**
 * Filtra un array eliminando los campos especificados en un segundo array.
 *
 * @param array $arrayCompleto El array original con todos los datos.
 * @param array $camposExcluir Los nombres de las claves a excluir.
 * @return array El array filtrado sin los campos especificados.
 */
function filtrarArray($arrayCompleto, $camposExcluir)
{
    // Usar array_diff_key para excluir las claves presentes en $camposExcluir
    return array_diff_key($arrayCompleto, array_flip($camposExcluir));
}
