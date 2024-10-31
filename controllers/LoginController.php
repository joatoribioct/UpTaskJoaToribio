<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login(Router $router) {
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            
            $alertas = $usuario->validarLogin();

            if(empty($alertas)) {
                //verificar que el usuario este exista
                $usuario  = Usuario::where('email', $usuario->email);
                if(!$usuario || !$usuario->confirmado ) {
                    Usuario::setAlerta('error', 'El Usuario No Existe o No esta Confirmado');
                } else {
                    // el usuario exite
                    if (password_verify($_POST['password'], $usuario->password)) {
                        //Iniciar Session 
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;
                        //redireccionar
                        header('Location: /dashboard');
                        
                    } else {
                        Usuario::setAlerta('error', 'La contraseña es incorrecta');
                        
                    }
                }

            }
            
        }
        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesion',
            'alertas' => $alertas
        ]);
    }

    public static function logout() {
        session_start();
        $_SESSION = [];

        header('Location: /');
    }

    public static function crear(Router $router) {
        $usuario = [];
        $usuario = new Usuario;

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            
            $alertas = $usuario->validarNuevaCuenta();

            if(empty($alertas)) {
                $exiteUsuario = Usuario::where('email', $usuario->email);
                if($exiteUsuario) {
                    Usuario::setAlerta('error' , 'El Usuario Existe');
                    $alertas = Usuario::getAlertas();
                } else {
                    //hashear password
                    $usuario->hashearPassword();

                    //eliminar elemento password 2
                    unset($usuario->password2);

                    //generar token
                    $usuario->crearToken();

                    //crear nuevo usuario
                    $resultado = $usuario->guardar();

                    //enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token );
                    $email->enviarConfirmacion();
                    

                    if($resultado) {
                        header('Location: /mensaje');
                    }
                }
            }
           
        }

        $router->render('auth/crear', [
            'titulo' => 'Crea tu Cuenta en UpTask',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router) {
        $alertas = [];
        $mostrar2 = true;

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if(empty($alertas)) {
                //buscar el usuario a reestablecer
                $usuario = Usuario::where('email', $usuario->email);

                if($usuario && $usuario->confirmado === "1") {
                    //encontre el usuario

                    // Generar un nuevo token
                    $usuario->crearToken();
                    unset($usuario->password2);
                    // Actualizar el usuario\
                    $usuario->guardar();
                    // Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token );
                    $email->enviarInstruciones();
                    // IMPRIMIR LA ALERTA
                    Usuario::setAlerta('exito', 'Hemos enviando las instruciones a tu email');
                    $mostrar2 = false;


                } else {
                    Usuario::setAlerta('error', 'El Usuario no Existe o no esta confirmado');
                }
            }
        }
        $alertas = Usuario::getAlertas();


        $router->render('auth/olvide', [
            'titulo' => 'Recuperar Password',
            'alertas' => $alertas,
            'mostrar2' => $mostrar2
        ]);
    }

    public static function restablecer(Router $router) {
        
        $token =s($_GET['token']);

        $mostrar = true;

        if(!$token) header('Location : /');
        // identificar token
        $usuario = Usuario::where('token', $token);
        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token No Valido');
            $mostrar = false;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            //añadir el nuevo password
            $usuario->sincronizar($_POST);
            //validar el password
            $alertas = $usuario->validarPassword();

            if(empty($alertas)) {
                //hashar el nuevo password

                $usuario->hashearPassword();

                //eliminar el token
                $usuario->token = null;
                //guardar usuario en la base de dato
                $resultado = $usuario->guardar();
                // Redireccionar
                
                if($resultado) {
                    header('Location: /');
                }
            }
        }
        $alertas = Usuario::getAlertas();

        $router->render('auth/restablecer', [
            'titulo' => 'Restablecer Password',
            'alertas' => $alertas,
            'mostrar'=> $mostrar
        ]);
    }
    public static function mensaje(Router $router) {
        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta creada correctamente'
        ]);
        
    }
    public static function confirmar(Router $router) {
        $token = s($_GET['token']);

        if(!$token) header('Location: /');
        //encontrar usaurio con el token

        $usuario = Usuario::where('token', $token);
        
        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token No Valido');
        } else {
            //confirmar la cuenta
            $usuario->confirmado = 1;
            $usuario->token = null;
            unset($usuario->password2);

            $usuario->guardar();

            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');
        }

        $alertas = Usuario::getAlertas();
        
        $router->render('auth/confirmar', [
            'titulo' => 'Confirma tu cuenta UpTask',
            'alertas' => $alertas
        ]);
    
    }
}