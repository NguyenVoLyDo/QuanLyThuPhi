<?php
namespace MVC;

class Model {

    /** @var \PDO */
    public $db;

    public function __construct() {
        $cfg = DATABASE;
        $dsn = "mysql:host={$cfg['Host']};dbname={$cfg['Name']};port={$cfg['Port']};charset=utf8mb4";
        try {
            $this->db = new \PDO($dsn, $cfg['User'], $cfg['Pass'], [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
        } catch (\PDOException $e) {
            throw new \Exception('Database connection failed: ' . $e->getMessage());
        }
    }
}
