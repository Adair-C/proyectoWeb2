<?php
require_once "../app/Helpers/Auth.php";
Auth::logout();
header("Location: ../public/login.php");
exit;
?>