<?php
require_once "../../app/Controllers/AdminController.php";
header("Content-Type: application/json");
$c = new AdminController();
$c->getAllMaterias();
?>