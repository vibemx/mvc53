<?php
class Model
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $attributes = array();
    protected $fillable = array();  // Agregado para columnas permitidas
    public function __construct(array $attributes = array())
    {
        $this->db = $this->connectDatabase();
        $this->fill($attributes);
    }

    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $this->attributes[$key] = $value;
            }
        }
    }
    public function getAttributes()
    {
        return $this->attributes;
    }
    protected function connectDatabase()
    {
        // Verificar que las constantes están definidas
        if (!defined('DB_HOST') || !defined('DB_NAME') || !defined('DB_USER') || !defined('DB_PASS')) {
            die(json_encode(["error" => "Faltan constantes de configuración de la base de datos."]));
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
            $this->attributes[$this->primaryKey] = $this->db->lastInsertId();
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
        $columns = !empty($columns) ? implode(", ", $columns) : (empty($instance->fillable) ? '*' : implode(", ", $instance->fillable));
        $sql = "SELECT TOP 1 {$columns} FROM {$instance->table} WHERE {$instance->primaryKey} = :id";
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

    public static function all( $columns = array())
    {
        $instance = new static();
        $columns = !empty($columns) ? implode(", ", $columns) : (empty($instance->fillable) ? '*' : implode(", ", $instance->fillable));
        $sql = "SELECT {$columns} FROM {$instance->table}";
        $stmt = $instance->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            // Construir la cláusula SELECT
            $sql = "SELECT {$fields} FROM {$table}";

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
                    $conditions[] = "{$field} = :{$param}";
                }
                $sql .= implode(' AND ', $conditions);
            }

            // Agregar ORDER BY si está definido
            if ($orderBy) {
                $sql .= " ORDER BY {$orderBy}";
            }

            // Agregar límite si está definido
            if ($limit) {
                $sql .= " LIMIT {$limit}";
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
            throw new Exception('Ocurrio un error al realizar la busqueda.');
        }
    }
}
