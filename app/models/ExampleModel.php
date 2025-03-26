<?php

class ExampleModel extends Model
{
    // Define el nombre de la tabla
    protected $table = 'roles';
    
    // Sobrescribe la clave primaria para esta tabla
    protected $primaryKey = 'id_rol';
    
    // Lista de columnas de la tabla (no atributos)
    protected $fillable = array(
        'id_rol',
        'descripcion'
    );

    // Constructor que pasa los atributos al modelo base
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }
        // Getter dinámico
        public function __get($key)
        {
            if (in_array($key, $this->fillable)) {
                return $this->attributes[$key]?$this->attributes[$key] : null;
            }
            return null;
        }
    
        // Setter dinámico
        public function __set($key, $value)
        {
            if (in_array($key, $this->fillable)) {
                $this->attributes[$key] = $value;
            }
        }
}
