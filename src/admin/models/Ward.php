<?php
    class Ward {
        private $pdo;
        public function __construct($pdo) {
            $this->pdo = $pdo;
        }

        public function getAll() {
            $stmt = $this->pdo->query("SELECT * FROM wards");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }