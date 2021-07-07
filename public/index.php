<?php
 
use Config\Database;            
     
use Middlewares\JsonMiddleware;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Middlewares\CorsMiddleware;
use Slim\Middleware\ErrorMiddleware;
use Slim\Middleware\RoutingMiddleware;
use Middlewares\Authentication\AuthMiddleware;

use Controllers\MateriaController;
use Controllers\UsuarioController;
use Controllers\AreaController;
use Controllers\CategoriaController;
use Controllers\ConsultoraController;
use Controllers\CorreoController;
use Controllers\CursoController;
use Controllers\CursoEventoController;
use Controllers\CursoFormadorController;
use Controllers\EstadoCuentaController;
use Controllers\EstadoCursoController;
use Controllers\FormadorController;
use Controllers\FormadorMateriaController;
use Controllers\HorarioDisponibleFormadorController;
use Controllers\LoginController;
use Controllers\CursoDiasController;
use Controllers\ProvinciaController;
use Servicios\ServicioGoogleCalendar;
use Servicios\NotificacionesOneSignal;
use Servicios\ServicioWordpress;
use ServicioSMS\ServicioSMS;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once  '../vendor/autoload.php';

$conn = new Database();
$app = AppFactory::create();

$app->setBasePath('/appformadores/backend/public');


$app->group('/auth', function (RouteCollectorProxy $group){

  $group->post('[/]', LoginController::class . ':ValidarUsuario');

})->add(new AuthMiddleware(['login']));


$app->group('/servicioSms', function (RouteCollectorProxy $group){

  $group->post('[/]', ServicioSMS::class . ':EnviarSms');

  $group->post('/validarNumero', ServicioSMS::class . ':ValidarNumero');

  $group->post('/validarCodigo', ServicioSMS::class . ':ValidarCodeEnviadoAlCelular');

})->add(new AuthMiddleware(['Formador', 'Consultora']));

$app->group('/servicioWordpress', function (RouteCollectorProxy $group){

  $group->get('/obtenerMiembros/{usuario}', ServicioWordpress::class . ':MembersWordpres');

})->add(new AuthMiddleware(['perfil_1', 'perfil_2']));



//$app->add(new AuthMiddleware(['Formador', 'Consultora']));
$app->add(new CorsMiddleware());
$app->add(new JsonMiddleware());
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(false, true, true);

$app->run();

