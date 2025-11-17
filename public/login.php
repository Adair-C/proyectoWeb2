<?php
require_once "../src/Auth.php";

// Si ya está logueado, lo mandamos a su zona
if (Auth::isLoggedIn()) {
    $rol = Auth::rol();
    switch ($rol) {
        case "alumno":
            header("Location: /proyectoWeb2/public/alumno/menu.php");
            break;
        case "maestro":
            header("Location: /proyectoWeb2/public/maestro/menu.php");
            break;
        case "superadmin":
            header("Location: /proyectoWeb2/public/superadmin/menu.php");
            break;
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión | Control Escolar</title>
    <link rel="stylesheet" href="assets/css/auth.css">
    <script src="assets/js/login.js" defer></script>
</head>
<body>

<div class="auth-page">
    <div class="auth-card">

        <div class="auth-card-header">
            <span class="auth-tag">
                Bienvenido de nuevo
            </span>
            <h1 class="auth-title">Iniciar sesión</h1>
            <p class="auth-subtitle">
                Accede con tu usuario o correo y contraseña.
            </p>
        </div>

        <form id="form-login" class="auth-form">
            <div class="form-row">
                <label for="username" class="form-label">Usuario o correo</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    class="form-input"
                    placeholder="Ej. ElChelos o tu@correo.com"
                    required
                >
            </div>

            <div class="form-row">
                <label for="password" class="form-label">Contraseña</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-input"
                    placeholder="Tu contraseña"
                    required
                >
            </div>

            <div id="feedback" class="feedback"></div>

            <button type="submit" class="btn-primary">
                Entrar
            </button>
        </form>

        <div class="auth-footer">
            ¿Aún no tienes cuenta?
            <a href="register.php">Crear cuenta</a>
        </div>

    </div>
</div>

</body>
</html>
