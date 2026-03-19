<?php
require 'db_connect.php';

$province_id = $_GET['province_id'];
$sql = 'SELECT * FROM district WHERE province_id = '.$province_id.'';
$data = getAll($sql);

$districts[0] = [
    'id' => null,
    'name' => 'District'
];
foreach ($data as $row) {
    $districts[] = [
        'id' => $row['district_id'],
        'name' => $row['name']
    ];
}
echo json_encode($districts);


?>