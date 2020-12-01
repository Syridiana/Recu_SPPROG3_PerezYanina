<?php

namespace App\Controllers;

require '../vendor/autoload.php';

use Exception;
use \Firebase\JWT\JWT; //namespace

class AuthJWT {

    private static $secretKey = 'recu_segundoparcial';
    
    /**
     * Crea un token con los claims especificados en el payload por los datos.
     *
     * @param   object $datos
     * @return  string Json Web Token (JWT).
     */
    public static function Login( $datos ) {

        $payload = [ 'data' => $datos ];

        return JWT::encode( $payload, self::$secretKey );

    }

    public static function ValidarToken( $jwt ) {
        
        try {
            
            $jwtDecodificado = JWT::decode( $jwt, self::$secretKey, ['HS256'] );


            
        } catch (\Throwable $e) {
            

            throw new Exception( $e->getMessage() );
            
        }
        
        return $jwtDecodificado;

    }

    public static function GetDatos ( $jwt ) {

        try {
            
            return JWT::decode( $jwt, self::$secretKey, ['HS256'] );

        } catch (\Throwable $e) {
            

            throw new Exception( $e->getMessage() );

        }

    }

}