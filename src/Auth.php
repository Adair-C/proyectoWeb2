<?php
// src/Auth.php

class Auth
{
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login(array $user): void
    {
        self::startSession();

        // Regenerar ID de sesión para evitar session fixation
        session_regenerate_id(true);

        $_SESSION["user_id"] = (int)$user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["rol"] = $user["rol"];
        // Nuevo: guardar nombre completo para mostrarlo en el menú
        if (isset($user["nombre_completo"])) {
            $_SESSION["nombre_completo"] = $user["nombre_completo"];
        }
    }

    public static function logout(): void
    {
        self::startSession();

        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                "",
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
    }

    public static function isLoggedIn(): bool
    {
        self::startSession();
        return isset($_SESSION["user_id"]);
    }

    public static function userId(): ?int
    {
        self::startSession();
        return $_SESSION["user_id"] ?? null;
    }

    public static function username(): ?string
    {
        self::startSession();
        return $_SESSION["username"] ?? null;
    }

    public static function nombreCompleto(): ?string
    {
        self::startSession();
        return $_SESSION["nombre_completo"] ?? null;
    }

    public static function rol(): ?string
    {
        self::startSession();
        return $_SESSION["rol"] ?? null;
    }
}
