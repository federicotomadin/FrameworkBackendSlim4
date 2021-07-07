<?php


namespace Middlewares\Authentication;

use Components\FuncionesGenerales;
use Components\GenericResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Components\Token;
use Slim\Psr7\Response;

class AuthMiddleware
{
    private $roleArray= array();

    public function __construct($roleArray)
    {

     if(is_countable($roleArray)) {
        for($i=0;$i<count($roleArray);$i++) {
            array_push($this->roleArray, $roleArray[$i]); 
           }
     }
     
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {

        if (in_array('login', $this->roleArray)) {

          $clave = trim($request->getHeaders()['Authorization'][0]);

          if ($clave == 'adiVinaMe@:') {

            $response = $handler->handle($request);
            $existingContent = (string) $response->getBody();
            $resp = new Response();
            $resp->getBody()->write($existingContent);
    
            return $resp;

          }
        }

        try {

        /* It should be obtained from the token payload. */
        $token = FuncionesGenerales::after("Bearer", trim($request->getHeaders()['Authorization'][0])) ?? "";
        
        $inputRole =  Token::getRole(trim($token));
        
        $valid = in_array($inputRole, $this->roleArray);

        /* Invalid credentials, returns Unauthorized. */
        if (!$valid) {
            $response = new Response();
            $response->getBody()->write(GenericResponse::obtain(false, "No posee privilegios suficientes.", $inputRole));
            return $response->withStatus(401);
        }

        /* Valid credentials, returns the content. */
        $response = $handler->handle($request);
        $existingContent = (string) $response->getBody();
        $resp = new Response();
        $resp->getBody()->write($existingContent);

        return $resp;
    } catch (\Throwable $th) {

        $response = new Response();
        $response->getBody()->write(GenericResponse::obtain(false, $th->getMessage(), null));
        $response->withStatus(500);
        return $response;
}
    }
}