<?php

namespace Controllers;


use Components\Token;
use Components\GenericResponse;
use Components\PassManager;
use Models\Usuario;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

error_reporting(error_reporting() & ~E_NOTICE);


class LoginController
{

    public function ValidarUsuario(Request $request, Response $response, $args)
    {

        try {

            $datos = $request->getParsedBody();

            $usuario = Usuario::where('usuario', $datos['username'])->get();

            if (!isset($usuario[0])) {
             $response->getBody()->write(GenericResponse::obtain(false, "Sin usuario", $usuario));
             return $response;
            }
            else{

                $token = Token::getToken($usuario[0]['id'], $usuario[0]['email'], $usuario[0]['nivel_membresia']);

                $response->getBody()->write(GenericResponse::obtain(true, '', $token));
                $response->withStatus(200);
                return $response;

            }

        } catch (\Throwable $th) {

            $response->getBody()->write(GenericResponse::obtain(false, $th->getMessage(), null));
            $response->withStatus(500);
            return $response;
        }
    }
}
