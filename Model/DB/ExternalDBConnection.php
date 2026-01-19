<?php
/**
 * Model/DB/ExternalDBConnection.php
 *
 * Singleton PDO para conectar a una base externa.
 */

class ExternalDBConnection
{
    /** @var PDO|null */
    private static $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO
    {
        if (self::$instance instanceof PDO) {
            return self::$instance;
        }

        require __DIR__ . '/../Config/credenciales_externa.php';

        $dsn = "mysql:host={$ext_host};port={$ext_port};dbname={$ext_dbname};charset=utf8mb4";

        self::$instance = new PDO($dsn, $ext_user, $ext_pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        return self::$instance;
    }

    public static function reset(): void
    {
        self::$instance = null;
    }
}
