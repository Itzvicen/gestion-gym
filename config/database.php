<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Ajusta la ruta según tu estructura de directorios

use Dotenv\Dotenv;

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();
        
        /* Conexion BBDD */
        $host = $_ENV['DB_HOST'];
        $user = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASSWORD'];
        $database = $_ENV['DB_DATABASE'];
        $charset = $_ENV['DB_CHARSET'];
        $dsn = "mysql:host=$host;dbname=$database;charset=$charset";

        try {
            $this->pdo = new PDO($dsn, $user, $password);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    // Evita la clonación del objeto
    private function __clone() {
    }
}