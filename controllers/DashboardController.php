<?php

namespace Controllers;

use Model\Proyectos;
use Model\Usuario;
use MVC\Router;

class DashboardController {
    public static function index(Router $router) {

        session_start();

        isAuth();

        $id = $_SESSION['id'];

        $proyectos = Proyectos::belongsTo('propietarioId',$id);
        

        $router->render('dashboard/index', [
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]); 
    }

    public static function crear_proyecto(Router $router) {
        session_start();

        isAuth();

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $proyecto = new Proyectos($_POST);
            //validacion
            $alertas = $proyecto->validarProyecto();

            if(empty($alertas)) {
                //gerenarr url unica
                $hash = md5(uniqid());
                $proyecto->url = $hash;

                //almacenar el creador del proyecto
                $proyecto->propietarioId = $_SESSION['id'];
                
                //guardar el proyecto
                $proyecto->guardar();

                //reedirecionar
                header('Location: /proyecto?id=' . $proyecto->url);
            }
        }
        $router->render('dashboard/crear-proyecto', [
            'alertas' => $alertas,
            'titulo' => 'Crear Proyecto'
        ]); 

    }

    public static function proyecto(Router $router) {
        session_start();
        isAuth();
        $token = $_GET['id'];
        
        if(!$token) header('Location: /dashboard');

        
        //revisar que la persona que visita sea la persona que lo creo
        $proyecto = Proyectos::where('url', $token);
        if($proyecto->propietarioId !== $_SESSION['id']) {
            header('Location: /dashboard');
        }
        
        $router->render('dashboard/proyecto', [
            'titulo' => $proyecto->proyecto
        ]); 
    }

    public static function perfil(Router $router) {
        session_start();
        isAuth();

        $alertas = [];
        $usuario =  Usuario::find($_SESSION['id']);

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);

            $alertas = $usuario->validar_perfil();

        

            if(empty($alertas)) {
                
                $usuarioExiste = Usuario::where('email', $usuario->email);

                if($usuarioExiste && $usuarioExiste->id !== $usuario->id){
                    Usuario::setAlerta('error', 'El Correo ya esta registrado no se puede modificar');
                    $alertas = $usuario->getAlertas();
                } else {
                    $usuario->guardar();

                    Usuario::setAlerta('exito', 'Guardado Correctamente');
                    $alertas = $usuario->getAlertas();
                    $_SESSION['nombre'] = $usuario->nombre;
                }
                
                
            }
        }
        
        $router->render('dashboard/perfil', [
            'titulo' => 'perfil',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]); 

    }

    public static function cambiar_password(Router $router) {
        session_start();
        isAuth();
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = Usuario::find($_SESSION['id']);
            $usuario->sincronizar($_POST);

            $alertas = $usuario->nuevo_password();

            if(empty($alertas)) {
                $resultado = $usuario->comprar_password();
                if($resultado) {

                    $usuario->password = $usuario->password_nueva;
                    //eliminar propiedades no necesarioas
                    unset($usuario->password_actual);

                    unset($usuario->password_nueva);
                    // hashear el nuevo password

                    $usuario->hashearPassword();

                    $resultado = $usuario->guardar();

                    if($resultado) {
                        Usuario::setAlerta('exito', 'Contraseña Cambiada Correctamente');
                        $alertas = $usuario->getAlertas();
                    }
                    // asingar nuevo password
                    


                } else {
                    Usuario::setAlerta('error', 'Contraseña Actual Incorrecta');
                    $alertas = $usuario->getAlertas();
                }
            }
            
        }



        $router->render('dashboard/cambiar-password', [
            'titulo' => 'Cambiar Contraseña',
            'alertas' => $alertas
        ]); 
    }

}