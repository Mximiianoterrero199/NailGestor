<?php

class Database
{
    private static ?PDO $conexion = null;

    public static function conectar(): PDO
    {
        if (self::$conexion === null) {
            // Configuración InfinityFree
            // ============================================
            // 1. REEMPLAZA EL HOST: En tu panel dirá algo como 'sql123.infinityfree.com' (NO es localhost)
            $host = 'sql200.infinityfree.com'; 
            
            // 2. Base de datos (Correcto, la que pasaste)
            $baseDatos = 'if0_42140306_nailgestor';
            
            // 3. Usuario: Suele ser el mismo prefijo de la base de datos
            $usuario = 'if0_42140306'; 
            
            // 4. Clave: Es tu contraseña principal de vPanel / cPanel de InfinityFree
            $clave = 'BrisaNails2026';

            $dsn = "mysql:host={$host};dbname={$baseDatos};charset=utf8mb4";

            self::$conexion = new PDO($dsn, $usuario, $clave, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }

        return self::$conexion;
    }
}
