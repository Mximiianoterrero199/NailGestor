<?php

class Database
{
    private static ?PDO $conexion = null;

    public static function conectar(): PDO
    {
        if (self::$conexion === null) {
            $env = require __DIR__ . '/../../env.php';

            $host = $env['DB_HOST'];
            $baseDatos = $env['DB_NAME'];
            $usuario = $env['DB_USER'];
            $clave = $env['DB_PASS'];

            $dsn = "mysql:host={$host};dbname={$baseDatos};charset=utf8mb4";

            self::$conexion = new PDO($dsn, $usuario, $clave, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }

        return self::$conexion;
    }
}
