<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use \Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . "/../poo/usuario.php";
require_once __DIR__ . "/../poo/auto.php";
require_once __DIR__ . "/../poo/MW.php";


$app = AppFactory::create();

$app->get('/', \Usuario::class . ':TraerTodos');

$app->get('/autos', \Auto::class . ':TraerTodos');

$app->post('/usuarios', \Usuario::class . ':AgregarUno')
->add(\MW::class . '::VerificarSiExisteCorreo')
->add(\MW::class . '::VerificarSiEstanVacios')
->add(\MW::class . ':ValidarCampo');

$app->post('/', \Auto::class . ':AgregarUno')
->add(\MW::class . ':middlewareExtrañamenteEspecifico');

$app->get('/login', \Usuario::class . ':ChequearJWT');

$app->post('/login', \Usuario::class . ':VerificarUsuario')
->add(\MW::class . ':VerificarSiExisteUsuario')
->add(\MW::class . '::VerificarSiEstanVacios')
->add(\MW::class . ':ValidarCampo');




$app->run();

?>