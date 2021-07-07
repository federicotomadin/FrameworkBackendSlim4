<?php

namespace Controllers;

use Components\GenericResponse;
use Models\Provincia;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use stdClass;

class ProvinciaController
{
    public function TraerProvincias(Request $request, Response $response, $args)
    {
        try {

            $resp = Provincia::all();

            $response->getBody()->write(GenericResponse::obtain(true, '', $resp));
            $response->withStatus(200);
            return $response;
        } catch (\Throwable $th) {

            $response->getBody()->write(GenericResponse::obtain(false, $th->getMessage(), null));
            $response->withStatus(500);
            return $response;
        }
    }
}
