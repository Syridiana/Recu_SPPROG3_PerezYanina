<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\Models\Turno;
use App\Models\Mascota;
use App\Models\User;
use App\Models\Factura;
use App\Components\Resultado;


$app = AppFactory::create();

class FacturaController{

    public function getALL(Request $request, Response $response) {

        $token = $_SERVER['HTTP_TOKEN'];
        $idCliente['id_cliente'] = AuthJWT::GetDatos($token)->id;

        $rta = Factura::where('id_cliente', $idCliente)->get();
       echo  json_encode($rta);


        return $response;
    }

    public function addOne(Request $request, Response $response) {
        $parserBody = $request->getParsedBody();
        $materia = new Turno;

        if( !isset($_POST['tipo']) || !isset($_POST['fecha']) ){
            $result = new Resultado(false,"ERROR. Debe ingresar todos los datos", 500);
            $response->getBody()->write(json_encode($result));
        }else{
            if( empty($parserBody['tipo'])  || empty($parserBody['fecha']) ){
                $result = new Resultado(false,"ERROR. Datos invalidos", 500);
                $response->getBody()->write(json_encode($result));
            }else{
                if(strtolower( $parserBody['tipo']) == 'perro' || strtolower($parserBody['tipo']) == 'gato' 
                || strtolower($parserBody['tipo']) == 'huron'){
                    try {
                        $turno = new Turno;
                        $turno['tipo'] = strtolower( $parserBody['tipo']);
                        $turno['fecha'] = $parserBody['fecha'];
                        $token = $_SERVER['HTTP_TOKEN'];
                        $turno['id_cliente'] = AuthJWT::GetDatos($token)->id;
                        $turno['atendido'] = false;

                        if(strtolower( $parserBody['tipo']) == 'perro')
                        {
                            $turno['id_tipo'] = 10;
                        } else if(strtolower( $parserBody['tipo']) == 'huron'){
                            $turno['id_tipo'] = 20;
                        } else{
                            $turno['id_tipo'] = 30;
                        }

                        $turno->save();
                        $result = new Resultado(true, $turno, 201);
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

    public function getTurno(Request $request, Response $response, $args)
    {

        $idTurno = $args['idTurno'] ?? '';

        try{
            $turno =  Turno::where('id', '=',  $idTurno)
            ->update(['atendido' => true]);
            
        }catch (\Throwable $th) {
            $result = new Resultado(false, "ERROR. No se pudo guardar", 500);
            $response->getBody()->write(json_encode($result));
        }

        $result = new Resultado(true, "Turno actializado", 200);
        $response->getBody()->write(json_encode($result));


        return $response;
    }
}