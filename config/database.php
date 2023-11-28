<?php
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        /* Conexion BBDD */
        $host = "eu-south-1.neatlycloud.es";
        $user = 'vicentesantiago_gym';
        $password = 'digbos-5razsi-zoCjum';
        $database = 'vicentesantiago_gymdb';
        $charset = 'utf8mb4';

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