<?php

namespace Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Components\GenericResponse;
use PHPMailer;
use Components\PassManager;

require("class.phpmailer.php");
require("class.smtp.php");
error_reporting(error_reporting() & ~E_NOTICE);

class CorreoController
{
    public function EnviarCorreoBienvenida(Request $request, Response $response, $args)
    {

        try {

        $nombre = $request->getParsedBody()['nombre'];
        $destinatario = $request->getParsedBody()['email'];

        // $telefono = $_POST["telefono"];
        // $asunto = $_POST["asunto"];

        $mensaje =  "Felicitaciones tu usuario fue dado de alta en El Portal de Formación!!";

        $email = "portalformacion@appelportaldeformacion.com";

        // Datos de la cuenta de correo utilizada para enviar v�a SMTP
        $smtpHost = "smtp.gmail.com";  // Dominio alternativo brindado en el email de alta 
        $smtpUsuario = "portalformacion@appelportaldeformacion.com";  // Mi cuenta de correo
        $smtpClave = "Formacion2021*";  // Mi contrase�a

        $mail = new PHPMailer();
        $mail->SMTPDebug  = 0;    
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->Port = 587;
        $mail->IsHTML(true);
        $mail->SMTPSecure = 'tls';      
        $mail->CharSet = "utf-8";

        // VALORES A MODIFICAR //
        $mail->Host = $smtpHost;
        $mail->Username = $smtpUsuario;
        $mail->Password = $smtpClave;

        $mail->AddEmbeddedImage('logo.png', 'logoimg', 'logo.png', 'base64', 'image/png');

        //Content 
        $mail->From = $email; // Email desde donde env�o el correo.
        $mail->FromName = "El Portal de Formación";
        $mail->AddAddress($destinatario); // Esta es la direcci�n a donde enviamos los datos del formulario

        $mail->Subject = "Completar registro como formador"; // Este es el titulo del email.
        
        $mail->Body = "
        <html> 
        
        <head>
        <link rel='preconnect' href='https://fonts.gstatic.com'>
        <link href='https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500&display=swap' rel='stylesheet'>
        
        <style>
        body {background-color: #e8d4b3;}
        h1,h2{color: #0e5c80;
             font-family: 'Playfair Display', serif;
             margin-top:30px;
            }
        p    {color: #0e5c80;}

        img{
            max-height:200px;
            max-width:200px;
        }

        </style>
        </head>

        <body style='background-color:#e4cca4;text-align: center;min-height:300px'> 

        <img  src='https://www.saladaapp.com.ar/assets//merlinkapp/assets/imagenes/logo.png' alt='OnlyPict' />

        <h1 >Felicitaciones {$nombre} tu usuario fue dado de alta en El Portal de Formación!!</h1>
    
        <!--<p>Nombre: {$nombre}</p>
    
        <p>Email: {$email}</p> -->
    
        <h2>Te enviamos el link para que termines de completar tus datos:  <a href='http://www.elportaldeformacion.com'>Haz click aquí!</a></h2>

        </body>    
        </html>";
        
        $mail->AltBody = "{$mensaje} \n\n "; // Texto sin formato HTML
        
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $estadoEnvio = $mail->Send();

        if ($estadoEnvio) {
            $respuesta =  "El correo fue enviado correctamente.";
        } else {
            $respuesta =  "Ocurrió un error inesperado.";

            $response->getBody()->write(GenericResponse::obtain(false, $respuesta, null));
            $response->withStatus(200);
            return $response;
        }   

            $response->getBody()->write(GenericResponse::obtain(true, $respuesta, null));
            $response->withStatus(200);
            return $response;
        }

        catch (\Throwable $th) {

            $response->getBody()->write(GenericResponse::obtain(false, $th->getMessage(), null));
            $response->withStatus(500);
            return $response;
        }

    }

    public static function RestablecerContraseña($emailDestinatario)
    {

       try {
      
            $token = PassManager::hash($emailDestinatario);
            $destinatario = $emailDestinatario;
    
            $mensaje =  "Restablecer contraseña con el siguiente link: ". "https://servidordeprueba.xyz/recuperarclave?". $token;

            $email = "portalformacion@appelportaldeformacion.com";

            // Datos de la cuenta de correo utilizada para enviar v�a SMTP
            $smtpHost = "smtp.gmail.com";  // Dominio alternativo brindado en el email de alta 
            $smtpUsuario = "portalformacion@appelportaldeformacion.com";  // Mi cuenta de correo
            $smtpClave = "Formacion2021*";  // Mi contrase�a
    
            $mail = new PHPMailer();
            $mail->SMTPDebug  = 0;    
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->Port = 587;
            $mail->IsHTML(true);
            $mail->SMTPSecure = 'tls';      
            $mail->CharSet = "utf-8";
    
            // VALORES A MODIFICAR //
            $mail->Host = $smtpHost;
            $mail->Username = $smtpUsuario;
            $mail->Password = $smtpClave;

    
            //Content 
            $mail->From = $email; // Email desde donde env�o el correo.
            $mail->FromName = "El Portal de Formación";
            $mail->AddAddress($destinatario); // Esta es la direcci�n a donde enviamos los datos del formulario
    
            $mail->Subject = "Resetear clave Merlin Connect"; // Este es el titulo del email.
            
            $mail->Body = "{$mensaje}";
            
            $mail->AltBody = "Restablecer contraseña \n\n "; // Texto sin formato HTML
            
    
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
    
            $estadoEnvio = $mail->Send();
    
            if ($estadoEnvio) {
                $respuesta =  "enviado";
            } else {
                $respuesta =  "error";
            }   
                return $respuesta;
            }
    
            catch (\Throwable $th) {

                return $respuesta;
            }

    }

public static function EmailCursoAceptado(Request $request, Response $response, $args) {

    try {

        $nombre = $request->getParsedBody()['nombre'];
        $destinatario = $request->getParsedBody()['email'];

        $mensaje =  "Tenés un nuevo curso";

        $email = "portalformacion@appelportaldeformacion.com";

        // Datos de la cuenta de correo utilizada para enviar v�a SMTP
        $smtpHost = "smtp.gmail.com";  // Dominio alternativo brindado en el email de alta 
        $smtpUsuario = "portalformacion@appelportaldeformacion.com";  // Mi cuenta de correo
        $smtpClave = "Formacion2021*";  // Mi contrase�a

        $mail = new PHPMailer();
        $mail->SMTPDebug  = 0;    
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->Port = 587;
        $mail->IsHTML(true);
        $mail->SMTPSecure = 'tls';      
        $mail->CharSet = "utf-8";

        // VALORES A MODIFICAR //
        $mail->Host = $smtpHost;
        $mail->Username = $smtpUsuario;
        $mail->Password = $smtpClave;

        $mail->AddEmbeddedImage('logo.png', 'logoimg', 'logo.png', 'base64', 'image/png');

        //Content 
        $mail->From = $email; // Email desde donde env�o el correo.
        $mail->FromName = "El Portal de Formación";
        $mail->AddAddress($destinatario); // Esta es la direcci�n a donde enviamos los datos del formulario

        $mail->Subject = "Nuevo curso creado"; // Este es el titulo del email.
        
        $mail->Body = "
        <html>  
            <head>
            <link rel='preconnect' href='https://fonts.gstatic.com'>
            <link href='https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500&display=swap' rel='stylesheet'>
            
            <style>
            body {background-image: url('https://merlin.com.es/assets/logoPNG/EmailMerlin.png');}
            h1,h2{color: #0e5c80;
                font-family: 'Playfair Display', serif;
                margin-top:30px;
                }
            p    {color: #0e5c80;}

            img{
                max-height:200px;
                max-width:200px;
            }

            </style>
            </head>

            <body style=background-image:url('https://merlin.com.es/assets/logoPNG/EmailMerlin.png');text-align: center;min-height:300x'> 

            <img  src='https://merlin.com.es/assets/logoPNG/MerlinConnect02.png' alt='OnlyPict' />

            <h1 >Felicitaciones {$nombre} te han agendado un nuevo curso</h1>

            <h2>Puedes acceder al mismo aqui: <a href='http://merlin.com.es'>Haz click aquí!</a></h2>

            </body>    
            </html>";
        
        $mail->AltBody = "{$mensaje} \n\n "; // Texto sin formato HTML
        
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $estadoEnvio = $mail->Send();

        if ($estadoEnvio) {
            $respuesta =  "El correo fue enviado correctamente.";
        } else {
            $respuesta =  "Ocurrió un error inesperado.";

            $response->getBody()->write(GenericResponse::obtain(false, $respuesta, null));
            $response->withStatus(200);
            return $response;
        }   

            $response->getBody()->write(GenericResponse::obtain(true, $respuesta, null));
            $response->withStatus(200);
            return $response;
        }

        catch (\Throwable $th) {

            $response->getBody()->write(GenericResponse::obtain(false, $th->getMessage(), null));
            $response->withStatus(500);
            return $response;
        }

}

public static function EmailFormadorAceptaCurso(Request $request, Response $response, $args) {

    try {

        $nombre = $request->getParsedBody()['nombre'];
        $destinatario = $request->getParsedBody()['email'];

        $mensaje =  $nombre + " aceptó el curso";

        $email = "portalformacion@appelportaldeformacion.com";

        // Datos de la cuenta de correo utilizada para enviar v�a SMTP
        $smtpHost = "smtp.gmail.com";  // Dominio alternativo brindado en el email de alta 
        $smtpUsuario = "portalformacion@appelportaldeformacion.com";  // Mi cuenta de correo
        $smtpClave = "Formacion2021*";  // Mi contrase�a

        $mail = new PHPMailer();
        $mail->SMTPDebug  = 0;    
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->Port = 587;
        $mail->IsHTML(true);
        $mail->SMTPSecure = 'tls';      
        $mail->CharSet = "utf-8";

        // VALORES A MODIFICAR //
        $mail->Host = $smtpHost;
        $mail->Username = $smtpUsuario;
        $mail->Password = $smtpClave;

        $mail->AddEmbeddedImage('logo.png', 'logoimg', 'logo.png', 'base64', 'image/png');

        //Content 
        $mail->From = $email; // Email desde donde env�o el correo.
        $mail->FromName = "El Portal de Formación";
        $mail->AddAddress($destinatario); // Esta es la direcci�n a donde enviamos los datos del formulario

        $mail->Subject = "Curso aceptado"; // Este es el titulo del email.
        
        $mail->Body = "
        <html>  
            <head>
            <link rel='preconnect' href='https://fonts.gstatic.com'>
            <link href='https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500&display=swap' rel='stylesheet'>
            
            <style>
            body {background-image: url('https://merlin.com.es/assets/logoPNG/EmailMerlin.png');}
            h1,h2{color: #0e5c80;
                font-family: 'Playfair Display', serif;
                margin-top:30px;
                }
            p    {color: #0e5c80;}

            img{
                max-height:200px;
                max-width:200px;
            }

            </style>
            </head>

            <body style=background-image:url('https://merlin.com.es/assets/logoPNG/EmailMerlin.png');text-align: center;min-height:300x'> 

            <img  src='https://merlin.com.es/assets/logoPNG/MerlinConnect02.png' alt='OnlyPict' />

            <h1 >Felicitaciones !!, {$destinatario} aceptó el curso que le enviaste</h1>

            </body>    
            </html>";
        
        $mail->AltBody = "{$mensaje} \n\n "; // Texto sin formato HTML
        
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $estadoEnvio = $mail->Send();

        if ($estadoEnvio) {
            $respuesta =  "El correo fue enviado correctamente.";
        } else {
            $respuesta =  "Ocurrió un error inesperado.";

            $response->getBody()->write(GenericResponse::obtain(false, $respuesta, null));
            $response->withStatus(200);
            return $response;
        }   

            $response->getBody()->write(GenericResponse::obtain(true, $respuesta, null));
            $response->withStatus(200);
            return $response;
        }

        catch (\Throwable $th) {

            $response->getBody()->write(GenericResponse::obtain(false, $th->getMessage(), null));
            $response->withStatus(500);
            return $response;
        }

}

}
