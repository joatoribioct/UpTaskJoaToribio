<?php

namespace Controllers;

use Model\Proyectos;
use Model\Tarea;
use MVC\Router;

class TareaController
{
    public static function index()
    {
        $proyectoId = $_GET['id'];

        if (!$proyectoId) header('location: /dashboard');

        $proyecto = Proyectos::where('url', $proyectoId);

        session_start();

        if (!$proyectoId || $proyecto->propietarioId !== $_SESSION['id']) header('Locaction: /404');

        $tareas = Tarea::belongsTo('proyectoId', $proyecto->id);

        echo json_encode(['tareas' => $tareas]);
    }

    public static function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            session_start();
            $proyectoId = $_POST['proyectoId'];
            $proyecto = Proyectos::where('url', $proyectoId);

            if (!$proyecto || $proyecto->propietarioId !== $_SESSION['id']) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un Error al Agregar la tarea'
                ];
                echo json_encode($respuesta);
                return;
            }
            // Todo bien, instaciar y crear la tarea
            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;
            $resultado = $tarea->guardar();
            $respuesta = [
                'tipo' => 'exito',
                'id' => $resultado['id'],
                'mensaje' => 'Tarea Creada Correctamente',
                'proyectoId' => $proyecto->id
            ];
            echo json_encode($respuesta);
        }
    }

    public static function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // validar que el proyecto exita
            $proyecto = Proyectos::where('url', $_POST['proyectoId']);
            session_start();
            if (!$proyecto || $proyecto->propietarioId !== $_SESSION['id']) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un Error al Actualizar la Tarea'
                ];
                echo json_encode($respuesta);
                return;
            }
            $tarea = new  Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;

            $resultado = $tarea->guardar();

            if ($resultado) {
                $respuesta = [
                    'tipo' => 'exito',
                    'id' => $tarea->id,
                    'proyectoId' => $proyecto->id,
                    'mensaje' => 'Actualizado Correctemente'
                ];

                echo json_encode(['respuesta' => $respuesta]);
            }
        }
    }

    public static function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // validar que el proyecto exita
            $proyecto = Proyectos::where('url', $_POST['proyectoId']);
            session_start();
            if (!$proyecto || $proyecto->propietarioId !== $_SESSION['id']) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un Error al Actualizar la Tarea'
                ];
                echo json_encode($respuesta);
                return;
            }
            $tarea = new  Tarea($_POST);

            $resultado = $tarea->eliminar();

            $resultado = [
                'resultado' => $resultado,
                'mensaje' => 'Eliminado Correctamente',
                'tipo' => 'exito'
            ];
            echo json_encode($resultado);
        }
    }
}
