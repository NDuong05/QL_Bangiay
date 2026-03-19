<?php
require 'db_connect.php';

$sql = "SELECT * FROM province";
$data = getAll($sql);

$provinces = [];
foreach ($data as $row) {
    $provinces[] = [
        'id' => $row['province_id'],
        'name' => $row['name']
    ];
}
echo json_encode($provinces);
?>