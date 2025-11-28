<?php

require_once __DIR__ . "/Auth.php";

class Middleware
{
    public static function requireLogin(): void
    {
        if (!Auth::isLoggedIn()) {
            header("Location: /proyectoWeb2/public/login.php");
            exit;
        }
    }

    /**
     * @param string|array $roles  Ej: 'alumno' o ['maestro', 'superadmin']
     */
    public static function requireRole($roles): void
    {
        self::requireLogin();

        $rolUsuario = Auth::rol();
        $roles = is_array($roles) ? $roles : [$roles];

        if (!in_array($rolUsuario, $roles, true)) {
            // Prohibido
            header("Location: /proyectoWeb2/public/403.php");
            exit;
        }
    }
}
