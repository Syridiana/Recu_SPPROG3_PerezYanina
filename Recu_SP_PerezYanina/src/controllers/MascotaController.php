<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\Models\Mascota;
use App\Components\Resultado;


$app = AppFactory::create();

class MascotaController{

    public function getALL(Request $request, Response $response) {
        $rta = Mascota::get();

        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function addOne(Request $request, Response $response) {
        $parserBody = $request->getParsedBody();
        $materia = new Mascota;

        if( !isset($_POST['tipo']) || !isset($_POST['precio']) ){
            $result = new Resultado(false,"ERROR. Debe ingresar todos los datos", 500);
            $response->getBody()->write(json_encode($result));
        }else{
            if( empty($parserBody['tipo'])  || empty($parserBody['precio']) ){
                $result = new Resultado(false,"ERROR. Datos invalidos", 500);
                $response->getBody()->write(json_encode($result));
            }else{
                if(strtolower( $parserBody['tipo']) == 'perro' || strtolower($parserBody['tipo']) == 'gato' 
                || strtolower($parserBody['tipo']) == 'huron'){
                    try {
                        $mascota = new Mascota;
                        $mascota['tipo'] = strtolower( $parserBody['tipo']);
                        $mascota['precio'] = $parserBody['precio'];

                        if(strtolower( $parserBody['tipo']) == 'perro')
                        {
                            $mascota['id_tipo'] = 10;
                        } else if(strtolower( $parserBody['tipo']) == 'huron'){
                            $mascota['id_tipo'] = 20;
                        } else{
                            $mascota['id_tipo'] = 30;
                        }

                        $mascota->save();
                        $result = new Resultado(true, $mascota, 201);
                        $response->getBody()->write(json_encode($result));
                    } catch (\Throwable $th) {
                        $result = new Resultado(false, "ERROR. No se pudo guardar", 500);
                        $response->getBody()->write(json_encode($result));
                    }
                }else{
                    $result = new Resultado(false,"ERROR. Tipo invalido", 500);
                    $response->getBody()->write(json_encode($result));
                }
            } 
        }
        return $response;
    }

}