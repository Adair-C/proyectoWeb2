<?php
require_once __DIR__ . '/../Models/Usuario.php';
require_once __DIR__ . '/../Helpers/Auth.php';

class AuthController {
    public function login() {
        $data = json_decode(file_get_contents("php://input"), true);
        $user = Usuario::findByUsernameOrEmail($data['username'] ?? '');

        if (!$user || !password_verify($data['password'] ?? '', $user['password'])) {
            echo json_encode(["error" => "Credenciales incorrectas"]);
            return;
        }
        if ($user['activo'] != 1) {
            echo json_encode(["error" => "Cuenta desactivada"]);
            return;
        }

        Auth::login($user);
        
        $redirect = match ($user['rol']) {
            'alumno' => '/proyectoWeb2/public/alumno/menu.php',
            'maestro' => '/proyectoWeb2/public/maestro/menu.php',
            'superadmin' => '/proyectoWeb2/public/superadmin/menu.php',
            default => ''
        };
        echo json_encode(["ok" => true, "redirect" => $redirect]);
    }

    public function register() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data['username']) || empty($data['password']) || empty($data['email'])) {
            echo json_encode(["error" => "Datos incompletos"]);
            return;
        }

        if (Usuario::findByUsernameOrEmail($data['username']) || Usuario::findByUsernameOrEmail($data['email'])) {
            echo json_encode(["error" => "Usuario o correo ya existe"]);
            return;
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        if (Usuario::create($data)) {
            echo json_encode(["ok" => true, "msg" => "Registro exitoso"]);
        } else {
            echo json_encode(["error" => "Error al registrar"]);
        }
    }
}