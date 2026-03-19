<?php
require 'db_connect.php';

$district_id = $_GET['district_id'];
$sql = "SELECT * FROM wards WHERE district_id = $district_id";
$data = getAll($sql);

$wards[0] = [
    'id' => null,
    'name' => 'Ward'
];
foreach ($data as $row) {
    $wards[] = [
        'id' => $row['wards_id'],
        'name' => $row['name']
    ];
}
echo json_encode($wards);

?>