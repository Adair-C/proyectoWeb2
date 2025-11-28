<?php
// Apuntamos a la nueva ubicación en app/Helpers
require_once "../app/Helpers/Auth.php";

Auth::logout();
header("Location: login.php");
exit;