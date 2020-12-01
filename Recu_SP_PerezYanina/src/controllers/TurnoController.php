<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\Models\Turno;
use App\Models\Mascota;
use App\Models\User;
use App\Components\Resultado;
use App\Models\Factura;

$app = AppFactory::create();

class TurnoController
{

    public function getALL(Request $request, Response $response)
    {
        $rta = Turno::get();
        $obj = (object)[];

        foreach ($rta as $turno) {
            $obj->tipo = $turno->tipo;
            $obj->fecha = $turno->fecha;

            try {
                $cliente = User::where("id", $turno->id_cliente)->first();

                if (!empty($cliente)) {
                    $obj->nombre_cliente =  $cliente->nombre;

                    $mascota = Mascota::where("id_tipo", $turno->id_tipo)->first();

                    if (!empty($mascota)) {
                        $obj->precio = '$' . $mascota->precio;
                        $response->getBody()->write(json_encode($obj));
                    } else {
                        $result = new Resultado(false, "ERROR. Tipo de mascota invalido", 500);
                        $response->getBody()->write(json_encode($result));
                    }
                } else {
                    $result = new Resultado(false, "ERROR. No se pudo encontrar al cliente", 500);
                    $response->getBody()->write(json_encode($result));
                }
            } catch (\Throwable $th) {
                $result = new Resultado(false, "ERROR. No se pudo encontrar al cliente", 500);
                $response->getBody()->write(json_encode($result));
            }
        }

        return $response;
    }

    public function addOne(Request $request, Response $response)
    {
        $parserBody = $request->getParsedBody();
        $materia = new Turno;

        if (!isset($_POST['tipo']) || !isset($_POST['fecha'])) {
            $result = new Resultado(false, "ERROR. Debe ingresar todos los datos", 500);
            $response->getBody()->write(json_encode($result));
        } else {
            if (empty($parserBody['tipo'])  || empty($parserBody['fecha'])) {
                $result = new Resultado(false, "ERROR. Datos invalidos", 500);
                $response->getBody()->write(json_encode($result));
            } else {
                if (
                    strtolower($parserBody['tipo']) == 'perro' || strtolower($parserBody['tipo']) == 'gato'
                    || strtolower($parserBody['tipo']) == 'huron'
                ) {
                    try {
                        $turno = new Turno;
                        $turno['tipo'] = strtolower($parserBody['tipo']);
                        $turno['fecha'] = $parserBody['fecha'];
                        $token = $_SERVER['HTTP_TOKEN'];
                        $turno['id_cliente'] = AuthJWT::GetDatos($token)->id;
                        $turno['atendido'] = false;

                        if (strtolower($parserBody['tipo']) == 'perro') {
                            $turno['id_tipo'] = 10;
                        } else if (strtolower($parserBody['tipo']) == 'huron') {
                            $turno['id_tipo'] = 20;
                        } else {
                            $turno['id_tipo'] = 30;
                        }

                        $turno->save();
                        $result = new Resultado(true, $turno, 201);
                        $response->getBody()->write(json_encode($result));
                    } catch (\Throwable $th) {
                        $result = new Resultado(false, "ERROR. No se pudo guardar", 500);
                        $response->getBody()->write(json_encode($result));
                    }
                } else {
                    $result = new Resultado(false, "ERROR. Tipo invalido", 500);
                    $response->getBody()->write(json_encode($result));
                }
            }
        }
        return $response;
    }

    public function getTurno(Request $request, Response $response, $args)
    {

        $idTurno = $args['idTurno'] ?? '';

        try {
            $turno =  Turno::where('id', '=',  $idTurno)
                ->update(['atendido' => true]);

            $turno =  Turno::where('id', '=',  $idTurno)->first();

            $factura = new Factura;

            $factura['id_cliente'] = $turno->id_cliente;
            $factura['fecha'] = $turno->fecha;

            $mascota = Mascota::where("id_tipo", $turno->id_tipo)->first();

            if (!empty($mascota)) {

                $factura['precio'] = $mascota->precio;

                $rta = $factura->save();

                $response->getBody()->write(json_encode($factura) . " Consulta facturada ");
            }else{
                $result = new Resultado(false, "ERROR. Tipo de mascota invalido", 500);
                $response->getBody()->write(json_encode($result));
            }

        } catch (\Throwable $th) {
            $result = new Resultado(false, "ERROR. No se pudo guardar", 500);
            $response->getBody()->write(json_encode($result));
        }

        $result = new Resultado(true, "Turno actualizado", 200);
        $response->getBody()->write(json_encode($result));


        return $response;
    }
}
