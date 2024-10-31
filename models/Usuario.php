<?php

namespace Model;

class Usuario extends ActiveRecord {
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nueva = $args['password_nueva'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
    }
    //validar el login del controlador
    public function validarLogin() {
        if(!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'El Email No Valido';
        }
        if(!$this->password) {
            self::$alertas['error'][] = 'El Password no puede ir vacio ';
        }
        
        return self::$alertas;

    }

    //validacion para cuenta nuevas
    public function validarNuevaCuenta() {
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El Nombre del Usuario es Obligatorio';
        }

        if(!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }

        if(!$this->password) {
            self::$alertas['error'][] = 'El Password no puede ir vacio ';
        }

        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El Password debe contener al menos 6 caracteres ';
        }

        if($this->password !== $this->password2)  {
            self::$alertas['error'][] = 'Los Passwords son Diferentes ';
        }

        return self::$alertas;
    }
    // valida un email
    public function validarEmail() {
        if(!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'El Email No Valido';
        }

        return self::$alertas;
    }

    //valida el password
    public function validarPassword() {
        if(!$this->password) {
            self::$alertas['error'][] = 'El Password no puede ir vacio ';
        }

        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El Password debe contener al menos 6 caracteres ';
        }

        return self::$alertas;

    }

    public function validar_perfil() {
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El Nombre es Obligatorio';
        }

        if(!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }

        return self::$alertas;
    }

    public  function nuevo_password() : array {
        if(!$this->password_actual) {
            self::$alertas['error'][] = 'El Password actual es Oblitorio';
        }

        if(!$this->password_nueva) {
            self::$alertas['error'][] = 'El Password Nuevo es Obligatorio';

        }

        if(strlen($this->password_nueva) < 6) {
            self::$alertas['error'][] = 'El Password debe contener al menos 6 caracteres ';
        }

        return self::$alertas;
    }
    //comprobar el password
    public function comprar_password() :bool {
        return password_verify($this->password_actual, $this->password);
    }

    // hashea el password
    public function hashearPassword() : void {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    //generar token
    public function crearToken() : void {
        $this->token = uniqid();
    }
}