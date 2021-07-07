<?php

namespace Controllers;

use Components\GenericResponse;
use Components\FuncionesGenerales;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class DocumentosController
{

  public function CargarDocumento(Request $request, Response $response, $args)
  {

    try {

        $extensiones_permitidas = array('image/jpeg', 'application/pdf','image/jpg', 'image/png', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $id_usuario  = $request->getParsedBody()['id_usuario'];
        $file_name = $_FILES["documento"]["name"];
        $file_tmp = $_FILES["documento"]["tmp_name"];
        $file_size =  $_FILES["documento"]["size"];
        $file_type =  $_FILES["documento"]["type"];


        $move_file = $_SERVER['DOCUMENT_ROOT'] . '/appformadores/backend/public/' . 'docs/';


        if (in_array($file_type, $extensiones_permitidas)) { 

            $resp = move_uploaded_file($file_tmp,  $move_file . $id_usuario . '_' .  $file_name);
            $ruta =  $move_file . $id_usuario . '_' .  $file_name;

        } 

        $response->getBody()->write(GenericResponse::obtain(true, $resp ? $ruta : 'Error' , $resp));
        $response->withStatus(200);
        return $response;
        
        } catch (\Throwable $th) { 

            $response->getBody()->write(GenericResponse::obtain(false, $th->getMessage(), null));
            $response->withStatus(500);
            return $response;
        }

    }

    public function BorrarDocumento(Request $request, Response $response, $args)
    {
  
      try { 

        $ruta_archivo = $request->getParsedBody()['ruta'];

        if (unlink($ruta_archivo)) {

            $response->getBody()->write(GenericResponse::obtain(true, 'Archivo borrado con éxito' , ''));
            $response->withStatus(200);
            return $response;

        } else {

            $response->getBody()->write(GenericResponse::obtain(true, 'El archivo no existe' , ''));
            $response->withStatus(200);
            return $response;
        }
          
        } catch (\Throwable $th) { 
  
              $response->getBody()->write(GenericResponse::obtain(false, $th->getMessage(), null));
              $response->withStatus(500);
              return $response;
          }
  
      }

      public function BorrarDocumentosPorIdUsuario(Request $request, Response $response, $args)
      {
    
        try { 

          $resp = false;  
          $ruta_archivo =  $_SERVER['DOCUMENT_ROOT'] . '/appformadores/backend/public/' . 'docs/' ;
          $files = array_diff(scandir($ruta_archivo), array('.', '..')); 

          foreach($files as $file){ 

            if (explode("_", $file)[0] == $args['id_usuario']) {

                unlink($ruta_archivo . $file);
                $resp = true;
         
            }
          }
  
          $response->getBody()->write(GenericResponse::obtain(true, $resp ? 'Se eliminaron los archivos' : 'No se encuentran archivos de ese usuario para eliminar' , $resp));
          $response->withStatus(200);
          return $response;
            
          } catch (\Throwable $th) { 
    
                $response->getBody()->write(GenericResponse::obtain(false, $th->getMessage(), null));
                $response->withStatus(500);
                return $response;
            }
    
        }

}

  ?>