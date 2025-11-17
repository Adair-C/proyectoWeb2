<?php
// api/logout.php
require_once __DIR__ . '/../src/Auth.php';
Auth::logout();
http_response_code(204); // sin contenido
