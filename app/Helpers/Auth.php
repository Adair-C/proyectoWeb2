<?php
class Auth {
    public static function startSession() {
        if (session_status() === PHP_SESSION_NONE) session_start();
    }
    public static function login($user) {
        self::startSession();
        $_SESSION["user_id"] = (int)$user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["rol"] = $user["rol"];
        $_SESSION["nombre_completo"] = $user["nombre_completo"];
    }
    public static function logout() {
        self::startSession();
        session_destroy();
    }
    public static function isLoggedIn() {
        self::startSession();
        return isset($_SESSION["user_id"]);
    }
    public static function user() {
        self::startSession();
        return $_SESSION;
    }
    public static function userId() { return self::user()['user_id'] ?? null; }
    public static function rol() { return self::user()['rol'] ?? null; }
    public static function nombreCompleto() { return self::user()['nombre_completo'] ?? null; }
}