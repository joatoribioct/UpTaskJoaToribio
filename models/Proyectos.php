<?php

namespace Model;

class Proyectos extends ActiveRecord {
    protected static $tabla = 'proyectos';
    protected static $columnasDB = ['id', 'propietarioId', 'url', 'proyecto'];

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->propietarioId = $args['propietarioId'] ?? '';
        $this->url = $args['url'] ?? '';
        $this->proyecto = $args['proyecto'] ?? '';
    }

    public function validarProyecto() {
        if(!$this->proyecto) {
            self::$alertas['error'][] = 'El nombre del Proyecto es Obligatorio';
        }
        return self::$alertas;
    }
}