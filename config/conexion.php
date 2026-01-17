<?php

    require_once 'server.php';
    class Conexion{
        private static ?PDO $conexion = null;

        private function __construct() {}

        public static function conectar(): ?PDO
        {
            if (self::$conexion === null) {
                $host     = DB_SERVER;
                $dbname   = DB_NAME;
                $user     = DB_USER;
                $password = DB_PASS;

                $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

                $opciones = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Modo de error seguro
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devuelve arrays asociativos
                    PDO::ATTR_PERSISTENT         => false,                  // Evita conexiones persistentes
                    PDO::ATTR_EMULATE_PREPARES   => false,                  // Usa prepared statements nativos
                ];

                try {
                    self::$conexion = new PDO($dsn, $user, $password, $opciones);
                } catch (PDOException $e) {
                    error_log("Error de conexión a la base de datos: " . $e->getMessage(), 0);
                    die("Error de conexión. Intente más tarde.");
                }
            }

            return self::$conexion;
        }
    }
