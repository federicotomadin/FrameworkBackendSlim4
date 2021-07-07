<?php



class MWparaCORS
{
	/**
   * @api {any} /HabilitarCORSTodos/  HabilitarCORSTodos
   * @apiVersion 0.1.0
   * @apiName HabilitarCORSTodos
   * @apiGroup MIDDLEWARE
   * @apiDescription  Por medio de este MiddleWare habilito que se pueda acceder desde cualquier servidor
   *
   * @apiParam {ServerRequestInterface} request  El objeto REQUEST.
 * @apiParam {ResponseInterface} response El objeto RESPONSE.
 * @apiParam {Callable} next  The next middleware callable.
   *
   * @apiExample Como usarlo:
   *   ->add(\verificador::class . ':HabilitarCORSTodos')
   */
  public function HabilitarCORSTodos($request, $response, $next) {
         
   $objDelaRespuesta= new stdclass();
   $objDelaRespuesta->respuesta="";
   
   if($request->isGet())
   {
   // $response->getBody()->write('<p>NO necesita credenciales para los get </p>');
    $response = $next($request, $response);
   }
   else
   {
             
      $arrayConToken = $request->getHeader('token');
      $token=$arrayConToken[0];
   
      try 
      {
         Token::verificarToken($token);
         $objDelaRespuesta->esValido=true;      
      }
      catch (Exception $e) {      
         //guardar en un log
         $objDelaRespuesta->excepcion=$e->getMessage();
         $objDelaRespuesta->esValido=false;     
      }	

      if($objDelaRespuesta->esValido)
      {	
      
         if($request->isPost())
         {		
            // el post sirve para todos los logueados		
            $payload=Token::ObtenerData($token);					    
            $response = $next($request, $response);
         }
         else
         {
            $payload=Token::ObtenerData($token);
            // DELETE,PUT y DELETE sirve para todos los logeados y admin
            if($payload->perfil=="Administrador")
            {				
               $response = $next($request, $response);
            }		           	
            else
            {	
               $objDelaRespuesta->respuesta="Solo administradores/as";
            }
         }		          
      }    
      else
      {
         $objDelaRespuesta->respuesta="Solo usuarios registrados";
         $objDelaRespuesta->elToken=$token;
      }  
   }		  
   if($objDelaRespuesta->respuesta!="")
   {
      return $response->withJson($objDelaRespuesta, 401);  			
   }
     
   return  $response
   ->withHeader('Access-Control-Allow-Origin', '*')
   ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
   ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
}

	/**
   * @api {any} /HabilitarCORS8080/  HabilitarCORS8080
   * @apiVersion 0.1.0
   * @apiName HabilitarCORS8080
   * @apiGroup MIDDLEWARE
   * @apiDescription  Por medio de este MiddleWare habilito que se pueda acceder desde http://localhost:8080
   *
   * @apiParam {ServerRequestInterface} request  El objeto REQUEST.
   * @apiParam {ResponseInterface} response El objeto RESPONSE.
   * @apiParam {Callable} next  The next middleware callable.
   *
   * @apiExample Como usarlo:
   *   ->add(\verificador::class . ':HabilitarCORS8080')
   */
	public function HabilitarCORS8080($request, $response, $next) {

		/*
		al ingresar no hago nada
		*/
		 $response = $next($request, $response);
		// $response->getBody()->write('<p>habilitado HabilitarCORS8080</p>');
   		 return $response
            ->withHeader('Access-Control-Allow-Origin', 'http://localhost:8080')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
	}

		/**
   * @api {any} /HabilitarCORS4200/  HabilitarCORS4200
   * @apiVersion 0.1.0
   * @apiName HabilitarCORS4200
   * @apiGroup MIDDLEWARE
   * @apiDescription  Por medio de este MiddleWare habilito que se pueda acceder desde http://localhost:4200
   *
   * @apiParam {ServerRequestInterface} request  El objeto REQUEST.
   * @apiParam {ResponseInterface} response El objeto RESPONSE.
   * @apiParam {Callable} next  The next middleware callable.
   *
   * @apiExample Como usarlo:
   *   ->add(\verificador::class . ':HabilitarCORS4200')
   */
	public function HabilitarCORS4200($request, $response, $next) {

		/*
		al ingresar no hago nada
		*/
		 $response = $next($request, $response);
		 //$response->getBody()->write('<p>habilitado HabilitarCORS4200</p>');
   		 return $response
            ->withHeader('Access-Control-Allow-Origin', 'http://localhost:4200')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
	}

	



}