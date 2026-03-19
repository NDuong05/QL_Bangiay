<?php 
session_start();
require_once("controller/db_controller/db_connect.php");
include_once("controller/controller.php");



if (isset($_POST['user'])) {
    $name = $_POST['user'];
}
else 
    $name ="";
?>

