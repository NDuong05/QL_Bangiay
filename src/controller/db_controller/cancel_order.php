<?php

require 'db_connect.php';

$order_id = $_POST['order_id'];
$sql = 'UPDATE orders SET Status = "Cancelled" WHERE OrderID = '.$order_id.'';
executeQuery($sql);

?>

