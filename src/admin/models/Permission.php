<?php
    class Permission {
        private $db;

        public function __construct($db) {
            $this->db = $db;
        }

        
        public function getAll() {
            $query = "SELECT * FROM vai_tro WHERE trangthai = 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
       
        public function getById($PermissionID) {
            $query = "SELECT * FROM vai_tro WHERE id = :permissionID";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':permissionID', $PermissionID, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        
        public function add($permissionName) {
            $query = "INSERT INTO vai_tro (ten_vai_tro) VALUES (:permissionName)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':permissionName', $permissionName, PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId();
        }

        
        public function update($permissionId, $permissionName) {
            $query = "UPDATE vai_tro SET ten_vai_tro = :permissionName WHERE id = :permissionId";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':permissionName', $permissionName, PDO::PARAM_STR);
            $stmt->bindParam(':permissionId', $permissionId, PDO::PARAM_INT);
            return $stmt->execute();
        }

        
        public function delete($RoleID) {
            $this->db->beginTransaction();
            try {
                $queryProduct = "DELETE FROM vai_tro WHERE id = :roleID";
                $stmtProduct = $this->db->prepare($queryProduct);
                $stmtProduct->bindParam(':roleID', $RoleID, PDO::PARAM_INT);
                $stmtProduct->execute();
                $this->db->commit();
                return true;
            } catch (Exception $e) {
                $this->db->rollBack();
                return false;
            }
        }

        public function addPermissionDetail($functionIDs , $RoleID){
            foreach ($functionIDs as $fid) {
                $stmt = $this->db->prepare("INSERT INTO chitiet_vaitro_quyen (id_vaitro, id_quyen) VALUES (:pid, :fid)");
                $stmt->bindParam(':pid', $RoleID, PDO::PARAM_INT);
                $stmt->bindParam(':fid', $fid, PDO::PARAM_INT);
                $stmt->execute();
            }
        }

        public function updatePermissionDetail($functionIDs, $RoleID) {
            // Delete old permissions
            $deleteStmt = $this->db->prepare("DELETE FROM chitiet_vaitro_quyen WHERE id_vaitro = :pid");
            $deleteStmt->bindParam(':pid', $RoleID, PDO::PARAM_INT);
            $deleteStmt->execute();
    
            foreach ($functionIDs as $fid) {
                $stmt = $this->db->prepare("INSERT INTO chitiet_vaitro_quyen (id_vaitro, id_quyen) VALUES (:pid, :fid)");
                $stmt->bindParam(':pid', $RoleID, PDO::PARAM_INT);
                $stmt->bindParam(':fid', $fid, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
        
        // chức năng
        public function getAllFunctions() {
            $query = "SELECT * FROM chuc_nang";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        // Lấy danh sách các quyền (theo mã chức năng) của vai trò
        public function getPermissionsByRole($RoleID) {
            $query = "SELECT ma_quyen FROM quyen WHERE id IN (SELECT id_quyen FROM chitiet_vaitro_quyen WHERE id_vaitro = :roleID)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':roleID', $RoleID, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN, 0); // Lấy cột đầu tiên (id_quyen)
        }

    }