<?php
error_reporting(E_ALL & ~E_WARNING);
ini_set('display_errors', 'on');
class ExampleController extends Controller
{
    // Método para obtener los resultados de la búsqueda
    public function index()
    {
        try {
            $resultados = ExampleModel::all();

            ApiResponse::success('Operación exitosa.', 200, $resultados);
        } catch (\Exception $th) {
            ApiResponse::error('No se encontraron resultados.');
        }
    }
    public function show($id = null)
    {
        // Comprobamos si se ha pasado un ID
        if ($id) {
            $resultados = ExampleModel::find($id);

            if (!$resultados) {
                ApiResponse::error('No se encontraron resultados.');
            }
            ApiResponse::success('Operación exitosa.', 200, $resultados->getAttributes());
        } else {
            ApiResponse::error('Parámetros incompletos.');
        }
    }
    // Crear un nuevo rol
    public function store()
    {
        try {
            // Recibir los datos de la solicitud
            $data = json_decode(file_get_contents("php://input"), true);

            if (empty($data['descripcion'])) {
                ApiResponse::error('El campo descripcion es obligatorio.', 400);
                return;
            }

            // Crear el nuevo rol
            $rol = new ExampleModel();
            $rol->descripcion = $data['descripcion'];
            $rol->save(); // Guarda en la base de datos

            // Respuesta exitosa
            ApiResponse::success('Rol creado exitosamente.', 201, $rol->getAttributes());
        } catch (\Exception $e) {
            // Manejo de errores
            ApiResponse::error('Error al crear el rol: ' . $e->getMessage(), 500);
        }
    }

    // Actualizar un rol existente
    public function update($id)
    {
        try {
            // Buscar el rol por su ID
            $rol = ExampleModel::find($id);

            if (!$rol) {
                ApiResponse::error('Rol no encontrado.', 404);
                return;
            }

            // Recibir los datos de la solicitud
            $data = json_decode(file_get_contents("php://input"), true);

            if (empty($data['descripcion'])) {
                ApiResponse::error('El campo descripcion es obligatorio.', 400);
                return;
            }

            // Actualizar los campos
            $rol->descripcion = $data['descripcion'];
            $rol->save(); // Guarda los cambios

            // Respuesta exitosa
            ApiResponse::success('Rol actualizado exitosamente.', 200, $rol->getAttributes());
        } catch (\Exception $e) {
            // Manejo de errores
            ApiResponse::error('Error al actualizar el rol: ' . $e->getMessage(), 500);
        }
    }
}
