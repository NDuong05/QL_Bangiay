<?php
    class Province {
        private $pdo;

        public function __construct() {
            global $pdo;
            $this->pdo = $pdo;
        }

        public function getAll() {
            $stmt = $this->pdo->query("SELECT * FROM province");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }