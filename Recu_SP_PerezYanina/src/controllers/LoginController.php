<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;
use \Firebase\JWT\JWT;
use Slim\Factory\AppFactory;
use App\Components\Resultado;
use App\Controllers\PassManager;

$app = AppFactory::create();

class LoginController{

    public function login(Request $request, Response $response) {

        $body = $request->getParsedBody();
        $email = $body['email'] ?? '';
        $clave = $body['clave'] ?? '';
        $nombre = $body['nombre'] ?? '';

        $clave = PassManager::Create($clave);

        $email = strtolower($email);
        $nombre = strtolower($nombre);

        if(isset($_POST['email'])){
            $exist =  User::where('email', $email)->first();
        }

        if(isset($_POST['nombre'])){
            $exist =  User::where('nombre', $nombre)->first();
        }

        if(!empty($exist)){

           $usuario = json_decode($exist);
            
           if($exist->clave == $clave){

            $Key = "recu_segundoparcial";
            $payload = array(   
                "id" => $usuario->id,
                "email" => $usuario->email,
                "nombre" => $usuario->nombre,
                "tipo" => $usuario->tipo
            );
            $jwt = JWT::encode($payload,$Key);

            $result = new Resultado(true,"TOKEN: ". $jwt, 200);
            $response->getBody()->write(json_encode($result));
           }else{
            $result = new Resultado(false,"ERROR. Datos incrrectos", 500);
            $response->getBody()->write(json_encode($result));
           }
        }else{
            $result = new Resultado(false,"ERROR. Email no encontrado", 500);
            $response->getBody()->write(json_encode($result));
        } 
        
        return $response;
    }

}