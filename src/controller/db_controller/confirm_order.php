<?php

require 'db_connect.php';

$order_id = $_POST['order_id'];
$sql = 'UPDATE orders SET Status = "Completed" WHERE OrderID = '.$order_id.'';
executeQuery($sql);

?>
