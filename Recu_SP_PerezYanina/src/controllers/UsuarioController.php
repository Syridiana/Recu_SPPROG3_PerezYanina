<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\Models\User;
use App\Components\Resultado;
use \Firebase\JWT\JWT;
use App\Controllers\PassManager;

$app = AppFactory::create();

class UsuarioController
{

    public function getALL(Request $request, Response $response)
    {
        $rta = User::get();

        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function addOne(Request $request, Response $response)
    {
        $parserBody = $request->getParsedBody();
        $user = new User;

        if (!isset($_POST['email']) || !isset($_POST['clave']) || !isset($_POST['tipo']) || !isset($_POST['nombre'])) {
            $result = new Resultado(false, "ERROR. Debe completar todos los campos", 500);
            $response->getBody()->write(json_encode($result));
        } else {
            if (empty($parserBody['email'])  || empty($parserBody['clave']) || empty($parserBody['tipo']) || empty($_POST['nombre'])) {

                $response->getBody()->write(json_encode("ERROR. Datos invalidos"));
            } else {
                if (strlen($_POST['clave']) >= 4) {
                    if ($parserBody['tipo'] == "admin" || $parserBody['tipo'] == "cliente") {
                        
                        if (!preg_match('/\s/', $parserBody['nombre'])) {

                            $existEmail =  User::where('email', trim($parserBody['email']))->first();
                            $existNombre =  User::where('nombre', trim($parserBody['nombre']))->first();


                            if (empty($existEmail) && empty($existNombre)) {
                                $user->email = trim(strtolower($parserBody['email']));

                                $user->clave = PassManager::Create($parserBody['clave']);

                                $user->tipo = $parserBody['tipo'];
                                $user->nombre = trim(strtolower($parserBody['nombre']));


                                try {
                                    $user->save();
                                    $result = new Resultado(true, $user, 201);
                                    $response->getBody()->write(json_encode($result));
                                } catch (\Throwable $th) {
                                    $result = new Resultado(false, "ERROR. No se pudo guardar", 500);
                                    $response->getBody()->write(json_encode($result));
                                }
                            } else {
                                $result = new Resultado(false, "ERROR. Email o nombre existente", 500);
                                $response->getBody()->write(json_encode($result));
                            }
                        } else {
                            $result = new Resultado(false, "ERROR:.Nombre de usuario no puede tener espacios en blanco", 500);
                            $response->getBody()->write(json_encode($result));
                        }
                    } else {
                        $result = new Resultado(false, "ERROR.  Tipo de usuario invalido", 500);
                    $response->getBody()->write(json_encode($result));
                    }
                } else {
                    $result = new Resultado(false, "ERROR. La clave es demasiado corta", 500);
                    $response->getBody()->write(json_encode($result));
                }
            }
        }
        return $response;
    }

    public function updateOne(Request $request, Response $response, $args)
    {
        $Key = "recu_segundoparcial";
        $token = $_SERVER['HTTP_TOKEN'];
        $decode = JWT::decode($token, $Key, array('HS256'));

        $user =  User::where('legajo', $args['legajo'])->first();
        $parserBody = $request->getParsedBody();

        if ($user) {

            switch ($decode->tipo) {
                case 1:
                    if (isset($_PUT['email']) || $parserBody['email'] != '') {
                        $user->email = $parserBody['email'];
                        try {
                            $user->save();
                            $result = new Resultado(true, $user, 201);
                            $response->getBody()->write(json_encode($result));
                        } catch (\Throwable $th) {
                            $result = new Resultado(false, "ERROR. No se pudo guardar", 500);
                            $response->getBody()->write(json_encode($result));
                        }
                    } else {
                        $result = new Resultado(false, "ERROR. Debe ingresar todos los datos", 500);
                        $response->getBody()->write(json_encode($result));
                    }
                    break;
                case 2:

                case 3:
            }
        } else {
            $result = new Resultado(false, "ERROR. Numero de legajo no encontrado.", 500);
            $response->getBody()->write(json_encode($result));
        }

        return $response;
    }

    public static function updateAlumno($args)
    {
    }
}
