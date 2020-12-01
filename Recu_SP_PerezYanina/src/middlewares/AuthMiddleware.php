<?php
namespace App\Middlewares;


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use \Firebase\JWT\JWT;
use App\Components\Resultado;

class AuthMiddleware {

    public $roles;

    public function __construct(string $role1, string $role2 = '', string $role3='')
    {
        $this->roles = array();
        array_push($this->roles, $role1,$role2,$role3);
    }

        /**
     * Example middleware invokable class
     *
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */

    public function __invoke(Request $request, RequestHandler $handler)
    {
     
        $valido = false;

        $token = $_SERVER['HTTP_TOKEN'];

        if(!empty($token)){


            $Key = "recu_segundoparcial";

            try {
                $decode = JWT::decode($token,$Key,array('HS256'));

                $valido = in_array($decode->tipo, $this->roles);

             } catch (\Throwable $th) {

             }
        }else{

        }

        
        if (!$valido) {
            $result = new Resultado(false,"ERROR. Usuario no autorizado", 403);
            
            
            $response = new Response();
            $response->getBody()->write(json_encode($result));
           
            return $response->withStatus(403);
        } else {
            $response = $handler->handle($request);

            $existingContent = (string) $response->getBody();

            $resp = new Response();
            $resp->getBody()->write($existingContent);
            return $resp;
        }
        
    }
}