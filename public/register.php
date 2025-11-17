<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro | Control Escolar</title>
    <!-- RUTA RELATIVA, sin / al inicio -->
    <link rel="stylesheet" href="assets/css/auth.css">
    <script src="assets/js/register.js" defer></script>
</head>
<body>

<div class="auth-page">
    <div class="auth-card">

        <div class="auth-card-header">
            <span class="auth-tag">
                Nuevo usuario
            </span>
            <h1 class="auth-title">Crear cuenta</h1>
            <p class="auth-subtitle">
                Regístrate como alumno o maestro para acceder al sistema.
            </p>
        </div>

        <form id="form-register" class="auth-form">
            <!-- Username -->
            <div class="form-row">
                <label for="username" class="form-label">Nombre de usuario</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    class="form-input"
                    placeholder="Ej. ElChelos"
                    required
                >
                <div class="form-hint">Este será tu usuario para iniciar sesión.</div>
            </div>

            <!-- Nombre completo -->
            <div class="form-row">
                <label for="nombre" class="form-label">Nombre completo</label>
                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    class="form-input"
                    placeholder="Nombre(s) y apellidos"
                    required
                >
            </div>

            <!-- Email -->
            <div class="form-row">
                <label for="email" class="form-label">Correo electrónico</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-input"
                    placeholder="tucorreo@ejemplo.com"
                    required
                >
            </div>

            <!-- Rol -->
            <div class="form-row">
                <label for="rol" class="form-label">Rol</label>
                <select
                    id="rol"
                    name="rol"
                    class="form-select"
                    required
                >
                    <option value="">Selecciona una opción</option>
                    <option value="alumno">Alumno</option>
                    <option value="maestro">Maestro</option>
                </select>
                <div class="form-hint">
                    El rol <strong>superadmin</strong> solo se asigna desde la administración.
                </div>
            </div>

            <!-- Contraseñas -->
            <div class="form-row form-inline">
                <div style="flex:1">
                    <label for="password" class="form-label">Contraseña</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        placeholder="Mínimo 8 caracteres"
                        required
                    >
                </div>
                <div style="flex:1">
                    <label for="password2" class="form-label">Confirmar contraseña</label>
                    <input
                        type="password"
                        id="password2"
                        name="password2"
                        class="form-input"
                        placeholder="Repite la contraseña"
                        required
                    >
                </div>
            </div>

            <!-- Mensajes desde AJAX -->
            <div id="feedback" class="feedback"></div>

            <!-- Botón -->
            <button type="submit" class="btn-primary">
                Crear cuenta
            </button>
        </form>

        <!-- 🔹 Referencia clara a INICIAR SESIÓN -->
        <div class="auth-footer">
            ¿Ya tienes una cuenta?
            <!-- Ruta relativa al mismo directorio: public/ -->
            <a href="login.php">Iniciar sesión</a>
        </div>

    </div>
</div>

</body>
</html>
