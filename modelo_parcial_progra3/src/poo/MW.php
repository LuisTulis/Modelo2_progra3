<?php

use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

require_once __DIR__ . "/autentificadora.php";
require_once "usuario.php";
require_once "auto.php";

class MW
{
    // Middleware Usuario
    public static function ValidarCampo(Request $request, RequestHandler $handler): ResponseMW
    {
        $campoPost = $request->getParsedBody();
        $obj_respuesta = new stdClass();
        $obj_respuesta->mensaje = "El campo no está setteado!";
        $obj_respuesta->status = 403;
        $obj = null;

        

        if(isset($campoPost["usuario_json"]))
        {
            $mensajeError = "No setteado: ";
            $parametrosNoSetteados = 0;

            $obj_respuesta->mensaje = "El campo está setteado!";
            $obj_respuesta->status = 200;

            $obj = json_decode($campoPost["usuario_json"]);

            if(!isset($obj->correo))
            {
                $mensajeError .= " - Correo";
                $parametrosNoSetteados++;
            }
            if(!isset($obj->clave))
            {
                $mensajeError .= " - Clave";
                $parametrosNoSetteados++;
            }

            if($parametrosNoSetteados != 0)
            {
                $obj_respuesta->mensaje = $mensajeError;
                $contenidoAPI = $obj_respuesta;
            }
            else
            {
                $response = $handler->handle($request);
                $contenidoAPI = (string) $response->getBody();
                $api_respuesta = json_decode($contenidoAPI);
                $obj_respuesta->status = $api_respuesta->status;
            }
            
        }

        $response = new ResponseMW();
        $response = $response->withStatus($obj_respuesta->status);
        $response->getBody()->write($contenidoAPI);

        return $response->withHeader('Content-Type', 'application/json');

    }

    public static function VerificarSiEstanVacios(Request $request, RequestHandler $handler): ResponseMW
    {
        $arrayParametros = json_decode($request->getParsedBody()["usuario_json"]);
        $obj_respuesta = new stdClass();
        $obj_respuesta->mensaje = "todo bien!!";
        $obj_respuesta->status = 200;
        $obj = null;

        if($arrayParametros->correo == "" || $arrayParametros == "")
        {
            $obj_respuesta->mensaje = "El correo y/o la clave estan vacios!";
            $obj_respuesta->status = 409;
        }
        else
        {
            $response = $handler->handle($request);
            $contenidoAPI = (string) $response->getBody();
            $api_respuesta = json_decode($contenidoAPI);
            $obj_respuesta->status = $api_respuesta->status;
        }

        $response = new ResponseMW();
        $response = $response->withStatus($obj_respuesta->status);
        $response->getBody()->write($contenidoAPI);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerificarSiExisteUsuario(Request $request, RequestHandler $handler): ResponseMW
    {
        $arrayDeParametros = $request->getParsedBody();
        $obj_respuesta = new stdClass();
        $obj_respuesta->mensaje = "El usuario no existe!";
        $obj_respuesta->status = 403;
        $obj = json_decode($arrayDeParametros["usuario_json"]);
        
        if (Usuario::TraerUsuario($obj)) 
        {
            $response = $handler->handle($request);
            $contenidoAPI = (string) $response->getBody();
            $api_respuesta = json_decode($contenidoAPI);
            $obj_respuesta->status = $api_respuesta->status;
        } 
        else 
        {
            $contenidoAPI = json_encode($obj_respuesta);
        }

        $response = new ResponseMW();
        $response = $response->withStatus($obj_respuesta->status);
        $response->getBody()->write($contenidoAPI);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function VerificarSiExisteCorreo(Request $request, RequestHandler $handler): ResponseMW
    {
        $arrayDeParametros = $request->getParsedBody();
        $obj_respuesta = new stdClass();
        $obj_respuesta->mensaje = "El correo ya existe existe!";
        $obj_respuesta->status = 403;
        $obj = json_decode($arrayDeParametros["usuario_json"]);
        
        if (!Usuario::TraerUsuarioPorCorreo($obj->correo)) 
        {
            $response = $handler->handle($request);
            $contenidoAPI = (string) $response->getBody();
            $api_respuesta = json_decode($contenidoAPI);
            $obj_respuesta->status = $api_respuesta->status;
        } 
        else 
        {
            $contenidoAPI = json_encode($obj_respuesta);
        }

        $response = new ResponseMW();
        $response = $response->withStatus($obj_respuesta->status);
        $response->getBody()->write($contenidoAPI);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function middlewareExtrañamenteEspecifico(Request $request, RequestHandler $handler): ResponseMW
    {
        $arrayDeParametros = $request->getParsedBody();
        $obj_respuesta = new stdClass();
        $obj_respuesta->mensaje = "Esta validación extrañamente especifica no fue superada.";
        $obj_respuesta->status = 409;
        $obj = json_decode($arrayDeParametros["auto_json"]);
        
        if ($obj->color != "azul" && $obj->precio > 50000 && $obj->precio < 600000) 
        {
            $response = $handler->handle($request);
            $contenidoAPI = (string) $response->getBody();
            $api_respuesta = json_decode($contenidoAPI);
            $obj_respuesta->status = $api_respuesta->status;
        } 
        else 
        {
            $contenidoAPI = json_encode($obj_respuesta);
        }

        $response = new ResponseMW();
        $response = $response->withStatus($obj_respuesta->status);
        $response->getBody()->write($contenidoAPI);
        return $response->withHeader('Content-Type', 'application/json');
    }

}
