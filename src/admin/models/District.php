<?php
    class District {
        private $pdo;

        public function __construct() {
            global $pdo;
            $this->pdo = $pdo;
        }

        public function getAll() {
            $stmt = $this->pdo->query("SELECT * FROM district");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }