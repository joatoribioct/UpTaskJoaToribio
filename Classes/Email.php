<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;
class Email {
    
    protected $email;
    protected $nombre;
    protected $token;



    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }
    public function enviarConfirmacion() {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'd767982b19abed';
        $mail->Password = 'd2710f092b90ca';

        $mail->setFrom('Cuentas@uptask.com');
        $mail->addAddress('Cuentas@uptask.com', 'uptask.com');
        $mail->Subject = 'Confirma tu cuenta';

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';


        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has creado tu cuenta en App UpTask, solo debes confirmarla presionando el siguiente enlace</p>";
        $contenido .= "<p>Presiona aqui: <a href= 'http://localhost:4000/confirmar?token=" . $this->token . "'>Confirmar cuenta</a> </p>";
        $contenido .= "<p>Si tu no solicitaste este cambio o esta cuenta puedes ignorar este mensaje</p>";
        $contenido .= "</html>";

        $mail->Body = $contenido;

        //EMVIAR

        $mail->send();

    }
    public function enviarInstruciones() {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'd767982b19abed';
        $mail->Password = 'd2710f092b90ca';

        $mail->setFrom('Cuentas@uptask.com');
        $mail->addAddress('Cuentas@uptask.com', 'uptask.com');
        $mail->Subject = 'Reestablece tu Contraseña';

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';


        $contenido = "<html>";
    
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has solicitado reestablecer tu contraseña,  presionando el siguiente enlace</p>";
        $contenido .= "<p>Presiona aqui: <a href= 'http://localhost:4000/restablecer?token=" . $this->token . "'>Reestablecer Contraseña</a> </p>";
        $contenido .= "<p>Si tu no solicitaste este cambio o esta cuenta puedes ignorar este mensaje</p>";
        $contenido .= "</html>";

        $mail->Body = $contenido;

        //EMVIAR

        $mail->send();
    }
}
