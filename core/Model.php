<?php
class Model
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $attributes = array();
    protected $fillable = array();  // Agregado para columnas permitidas
    protected $hidden = array();   // Columnas que no se devuelven pero se pueden usar internamente
    public function __construct(array $attributes = array())
    {
        $this->db = $this->connectDatabase();
        $this->fill($attributes);
    }

    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillable) || in_array($key, $this->hidden)) {
                $this->attributes[$key] = $value;
            }
        }
    }
    public function getAttributes()
    {
        return array_diff_key($this->attributes, array_flip($this->hidden));
    }
    protected function connectDatabase()
    {
        // Verificar que las constantes están definidas
        if (!defined('DB_HOST') || !defined('DB_NAME') || !defined('DB_USER') || !defined('DB_PASS')) {
            die(json_encode(array("error" => "Faltan constantes de configuración de la base de datos.")));
        }

        try {
            $dsn = "dblib:host=" . DB_HOST . ";dbname=" . DB_NAME;
            $db = new PDO($dsn, DB_USER, DB_PASS, array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ));
            return $db;
        } catch (PDOException $e) {
            throw new Exception("Sin conexión: " . $e->getMessage());
        }
    }
/**
 * Ejecuta una instrucción de control de transacciones en SQL Server usando PDO.
 *
 * Esta función permite controlar manualmente el flujo de una transacción SQL:
 * iniciar (`BEGIN TRANSACTION`), confirmar (`COMMIT`) o revertir (`ROLLBACK`).
 * Además, al iniciar una transacción (`begin`), establece `XACT_ABORT ON`
 * para garantizar que cualquier error dentro de la transacción la revierta automáticamente.
 *
 * @param string $type Tipo de operación de transacción a ejecutar. Valores permitidos:
 *                     - 'begin'   → Inicia una transacción.
 *                     - 'commit'  → Confirma la transacción.
 *                     - 'rollback'→ Revierte la transacción.
 *
 * @return bool `true` si la operación fue exitosa, `false` si ocurrió un error.
 *
 * @throws Exception Si se pasa un tipo inválido o ocurre un error en la ejecución SQL.
 *     
 */
protected function transaction($type)
{
    try {
        switch (strtolower($type)) {
            case 'begin':
                // Activa el modo XACT_ABORT para que cualquier error SQL cancele la transacción automáticamente
                $this->db->exec("SET XACT_ABORT ON;");
                $this->db->exec("BEGIN TRANSACTION;");
                break;

            case 'commit':
                $this->db->exec("COMMIT;");
                break;

            case 'rollback':
                $this->db->exec("ROLLBACK;");
                break;

            default:
                throw new Exception("Tipo de transacción no válido: " . $type);
        }
        return true;
    } catch (Exception $e) {
        echo "Error en la transacción ($type): " . $e->getMessage();
        return false;
    }
}

    protected function getLastInsertId()
    {
        $stmt = $this->db->query("SELECT SCOPE_IDENTITY() AS last_id");
        $row = $stmt->fetch();
        return $row ? $row['last_id'] : null;
    }
    public function checkConnection()
    {
        if (!$this->db) {
            throw new Exception("No se pudo establecer una conexión a la base de datos.");
        }
        return true;
    }

    public function save()
    {
        if (isset($this->attributes[$this->primaryKey])) {
            return $this->updateRecord();
        } else {
            return $this->insertRecord();
        }
    }

    protected function insertRecord()
    {
        $columns = implode(', ', array_keys($this->attributes));
        $placeholders = ':' . implode(', :', array_keys($this->attributes));
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";

        $stmt = $this->db->prepare($sql);
        if ($stmt->execute($this->attributes)) {
            $this->attributes[$this->primaryKey] = $this->getLastInsertId();
            return true;
        }
        return false;
    }

    protected function updateRecord()
    {
        $set = "";
        foreach ($this->attributes as $column => $value) {
            if ($column == $this->primaryKey) {
                continue;
            }
            $set .= "$column = :$column, ";
        }
        $set = rtrim($set, ", ");
        $sql = "UPDATE {$this->table} SET $set WHERE {$this->primaryKey} = :{$this->primaryKey}";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($this->attributes);
    }

    public function delete()
    {
        if (!isset($this->attributes[$this->primaryKey])) {
            return false;
        }
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :{$this->primaryKey}";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(array($this->primaryKey => $this->attributes[$this->primaryKey]));
    }

    public static function find($id, $columns = array())
    {
        $instance = new static();  // Crea una nueva instancia del modelo
        // Si no se especifican columnas, usamos solo los `fillable`
        if (empty($columns)) {
            $columns = $instance->fillable;
        }

        // Asegurar que se seleccionen también los `hidden` internamente
        $columns = array_merge($columns, $instance->hidden);

        $sql = "SELECT " . implode(", ", $columns) . " FROM {$instance->table} WHERE {$instance->primaryKey} = :id";
        $stmt = $instance->db->prepare($sql);
        $stmt->execute(array('id' => $id));
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($record) {
            // Al llamar a fill, los datos se asignan a los atributos del modelo
            $instance->fill($record);
            return  $instance;
        }
        return null;
    }

    public static function all($columns = array())
    {
        $instance = new static();

        // Si no se especifican columnas, usamos solo los `fillable`
        if (empty($columns)) {
            $columns = $instance->fillable;
        }

        // Asegurar que se seleccionen también los `hidden` internamente
        $columns = array_merge($columns, $instance->hidden);

        $sql = "SELECT " . implode(", ", $columns) . " FROM {$instance->table}";
        $stmt = $instance->db->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Filtrar las columnas ocultas antes de devolver los datos
        return array_map(function ($record) use ($instance) {
            return array_diff_key($record, array_flip($instance->hidden));
        }, $results);
    }
    public static function search($filters = array(), $columns = null, $orderBy = null, $limit = '')
    {
        $instance = new static();
        // Si no se proporcionan columnas, usar los atributos (fillable) o '*' si no están definidos
        if (is_null($columns)) {
            // Inicializar un arreglo para almacenar las columnas
            $columnsArray = array();

            // Si el modelo tiene columnas 'fillable' definidas, las usamos
            if (!empty($instance->fillable)) {
                // Si 'fillable' tiene datos, las agregamos a la lista de columnas
                $columnsArray = array_merge($columnsArray, $instance->fillable);
            }

            // Si el modelo tiene columnas 'hidden' definidas, también las agregamos a la lista de columnas
            if (!empty($instance->hidden)) {
                // Las columnas 'hidden' deben agregarse a la lista de columnas, pero no deben ser excluidas.
                $columnsArray = array_merge($columnsArray, $instance->hidden);
            }

            // Si se agregaron columnas, las unimos con comas para la consulta SQL
            if (!empty($columnsArray)) {
                $columns = implode(", ", $columnsArray);
            } else {
                // Si no hay 'fillable' ni 'hidden', usamos '*' para seleccionar todas las columnas
                $columns = '*';
            }
        }
        // Agregar límite si está definido
        if ($limit) {
            $limit = " TOP {$limit}";
        }
        // Construir la cláusula SELECT
        $sql = "SELECT {$limit} {$columns} FROM {$instance->table}";

        // Si hay filtros, agregar la cláusula WHERE
        if (!empty($filters)) {
            $sql .= " WHERE ";
            $conditions = array();
            foreach ($filters as $field => $value) {
                // Escapar el nombre del parámetro
                $param = str_replace(array('.', '[', ']'), '_', $field);
                $conditions[] = "{$field} like :{$param}";
            }
            $sql .= implode(' AND ', $conditions);
        }

        // Agregar ORDER BY si está definido
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }



        try {
            // Preparar la consulta
            $stmt = $instance->db->prepare($sql);

            // Asociar los valores de los filtros
            if (!empty($filters)) {
                foreach ($filters as $field => $value) {
                    $param = str_replace(array('.', '[', ']'), '_', $field);
                    $stmt->bindValue(":{$param}", $value);
                }
            }

            // Ejecutar la consulta
            $stmt->execute();
            // Retornar las instancias del modelo
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $errorMessage = "SQL Error: " . $e->getMessage() .
                " | Code: " . $e->getCode() .
                " | File: " . $e->getFile() .
                " | Line: " . $e->getLine();

            throw new Exception($errorMessage);
           // throw new Exception('Ocurrio un error al realizar la búsqueda.');
        }
    }


    /**
     * Función para realizar un SELECT con la posibilidad de agregar JOINS, WHERE, etc.
     * 
     * @param string $table   - El nombre de la tabla.
     * @param string $fields  - Los campos a seleccionar. Default '*'.
     * @param array  $joins   - Array con los joins a realizar, de forma similar a ['type', 'table', 'on'].
     * @param array  $where   - Array con las condiciones del WHERE, de la forma ['campo' => 'valor'].
     * @param string $orderBy - Columna por la cual ordenar (opcional).
     * @param string $limit   - Límite de resultados (opcional).
     * 
     * @return array - El resultado de la consulta.
     */
    public static function select($table, $fields = '*', $joins = array(), $where = array(), $orderBy = null, $limit = null)
    {
        $instance = new static();
        try {
            if ($limit) {
                $limit = " TOP {$limit}";
            }
            // Construir la cláusula SELECT
            $sql = "SELECT {$limit} {$fields} FROM {$table}";

            // Agregar los JOINs si existen
            if (!empty($joins)) {
                foreach ($joins as $join) {
                    // Cada $join debe contener ['type', 'table', 'on']
                    $sql .= " {$join['type']} JOIN {$join['table']} ON {$join['on']}";
                }
            }

            // Agregar la cláusula WHERE si existe
            if (!empty($where)) {
                $sql .= " WHERE ";
                $conditions = array();
                foreach ($where as $field => $value) {
                    // Escapar el nombre del parámetro, eliminando alias y caracteres no permitidos
                    $param = str_replace(array('.', '[', ']'), '_', $field);
                    $conditions[] = "{$field} like :{$param}";
                }
                $sql .= implode(' AND ', $conditions);
            }

            // Agregar ORDER BY si está definido
            if ($orderBy) {
                $sql .= " ORDER BY {$orderBy}";
            }



            // Preparar la consulta
            $stmt = $instance->db->prepare($sql);

            // Asociar los valores del WHERE
            if (!empty($where)) {
                foreach ($where as $field => $value) {
                    $param = str_replace(array('.', '[', ']'), '_', $field);
                    $stmt->bindValue(":{$param}", $value);
                }
            }

            // Ejecutar la consulta
            $stmt->execute();

            // Retornar los resultados como un arreglo asociativo
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }
    public static function paginar($tabla = null, $columns = null, $joins = '', $condiciones = '1=1', $orderBy = 'id', $page = 1, $limit = 50)
    {
        $instance = new static();
        if (empty($columns)) {
            $columns = implode(", ", $instance->fillable);
        }
        if (empty($tabla)) {
            $tabla = $instance->table;
        }
        try {
            $offset = ($page - 1) * $limit;

            // Construcción de la consulta con ROW_NUMBER para paginación
            $query = "SELECT * FROM (
                    SELECT ROW_NUMBER() OVER (ORDER BY $orderBy) AS row_num, $columns
                    FROM $tabla
                    $joins
                    WHERE $condiciones
                  ) AS paginated
                  WHERE row_num BETWEEN " . ($offset + 1) . " AND " . ($offset + $limit);

            $result = self::query($query);

            // Consulta para contar el total de registros
            $countQuery = "SELECT COUNT(1) AS total FROM $tabla $joins WHERE $condiciones";
            $countResult = self::query($countQuery);
            $totalRecords = $countResult[0]['total'];

            // Formato de respuesta
            return array(
                    "pagination"         => array(
                        "total"         => $totalRecords,
                        "per_page"      => $limit,
                        "current_page"  => $page,
                        "last_page"     => ceil($totalRecords / $limit),
                        "next_page_url" => $page < ceil($totalRecords / $limit) ? $page + 1 : null,
                        "prev_page_url" => $page == 1 ? 1 : $page - 1,
                    ),
                
                "data"          => $result
            );
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public static function query($query = null)
    {
        $instance = new static();
        if (!$query) {
            throw new Exception("No se proporcionó una consulta.");
        }

        try {
            $stmt = $instance->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Construir el mensaje de error manualmente
            $errorMessage = "SQL Error: " . $e->getMessage() .
                " | Code: " . $e->getCode() .
                " | File: " . $e->getFile() .
                " | Line: " . $e->getLine();

            throw new Exception($errorMessage);
        }
    }
}
