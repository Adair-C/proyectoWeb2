<?php
require_once "../app/Controllers/AuthController.php";
header("Content-Type: application/json");
$auth = new AuthController();
$auth->login();
?>