<?php
require_once "../../app/Controllers/AlumnoController.php";
header("Content-Type: application/json");
$c = new AlumnoController();
$c->getInscritas();
?>