<?php

namespace Components;

use \Firebase\JWT\JWT;
use Components\GenericResponse;
use Exception;
include_once '../vendor/firebase/php-jwt/src/JWT.php';



require __DIR__ . '/../../vendor/autoload.php';

class Token
{
    private static $key = 'aDiVinaMee';
    private static $tipoEncriptacion = ['HS256'];
    private static $aud = null;

    public static function getToken($id, $email, $nivel_membresia = null)
    {

        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $ahora = time();

        $payload = array(
        	'iat'=> $ahora,
            'exp' => $ahora + (60*60*24*60), //TOKEN VENCE A LOS DOS MESES
            'aud' => self::Aud(),
            'email' => $email,
            'id' => $id,
            'nivel_membresia' => $nivel_membresia,
            'app'=> "APP FORMADORES"
        );

        return JWT::encode($payload, Token::$key);
    }

    public static function getAud($token)
    {
        if ($token && !empty($token)) {
            $decoded = JWT::decode($token, Token::$key, array('HS256'));
            return $decoded->data->aud;
        } else {
            return null;
        }
    }

    public static function getExpiracion($token)
    {
        if ($token && !empty($token)) {
            $decoded = JWT::decode($token, Token::$key, array('HS256'));
            return $decoded->data->exp;
        } else {
            return null;
        }
    }

    public static function getRole($token)
    {
        if ($token && !empty($token)) {
            $decoded = JWT::decode($token, Token::$key, array('HS256'));
            return $decoded->nivel_membresia;
        } else {
            return null;
        }
    }

    public static function getEmail($token)
    {
        if ($token && !empty($token)) {
            $decoded = JWT::decode($token, Token::$key, array('HS256'));
            return $decoded->data->email;
        } else {
            return null;
        }
    }

    public static function getId($token)
    {
        if ($token && !empty($token)) {
            $decoded = JWT::decode($token, Token::$key, array('HS256'));
            return $decoded->data->id;
        } else {
            return null;
        }
    }

    public static function validateToken($token)
    {
        $decoded = JWT::decode($token, Token::$key, array('HS256'));
        return true;
    }

    private static function Aud()
    {
        $aud = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }
        
        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();
        
        return $aud;
        // return sha1($aud);
    }

    public static function VerificarToken($token)
    {
       
        if(empty($token)|| $token=="")
        {
            throw new Exception("El token esta vacio.");
        } 

        try {
            $decodificado = JWT::decode(
            $token,
            Token::$key,
            self::$tipoEncriptacion
            );
        } catch (Exception $e) {

           throw new Exception("Clave fuera de tiempo");
        }
        
        // si no da error,  verifico los datos de AUD que uso para saber de que lugar viene  
        // if($decodificado->aud !== self::Aud())
        // {
        //     throw new Exception("No es el usuario valido"+$decodificado->aud);
        // }
    }

    public static function ObtenerPayLoad($token)
    {
        return JWT::decode(
            $token,
            Token::$key,
            self::$tipoEncriptacion
        );
    }
     public static function ObtenerData($token)
    {
        return JWT::decode(
            $token,
            Token::$key,
            self::$tipoEncriptacion
        )->data;
    }

}
