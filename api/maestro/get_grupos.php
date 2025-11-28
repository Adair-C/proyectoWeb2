<?php
require_once "../../app/Controllers/MaestroController.php";
header("Content-Type: application/json");
$c = new MaestroController();
$c->getGrupos();
?>