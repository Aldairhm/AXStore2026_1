<?php

    class Encriptar{

        static function openCypher($action='encrypt',$string=false){
            $action = trim($action);
            $output = false;

            $myKey = 'oW%c76+jb2';
            $myIV = 'A)2!u467a^';
            $encrypt_method = 'AES-256-CBC';

            $secret_key = hash('sha256',$myKey);
            $secret_iv = substr(hash('sha256',$myIV),0,16);

            if ( $action && ($action == 'encrypt' || $action == 'decrypt') && $string )
            {
                $string = trim(strval($string));

                if ( $action == 'encrypt' )
                {
                    $output = openssl_encrypt($string, $encrypt_method, $secret_key, 0, $secret_iv);
                };

                if ( $action == 'decrypt' )
                {
                    $output = openssl_decrypt($string, $encrypt_method, $secret_key, 0, $secret_iv);
                };
            };

            return $output;
        }
    }

    /* Ejemplos de uso
    $cadena = "uES@ds3232#2@hgh";
    $encriptada = Encriptar::openCypher('encrypt', $cadena);
    echo "Cadena encriptada: ". $encriptada. "<br>";
    $desencriptada = Encriptar::openCypher('decrypt', $encriptada);
    echo "Cadena desencriptada: ". $desencriptada;
    */

