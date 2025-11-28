<?php
require_once __DIR__ . '/../Models/Usuario.php';
require_once __DIR__ . '/../Models/Materia.php';
require_once __DIR__ . '/../Helpers/Auth.php';

class AdminController {
    public function __construct() {
        if (!Auth::isLoggedIn() || Auth::rol() !== 'superadmin') {
            http_response_code(403); exit(json_encode(["error" => "No autorizado"]));
        }
    }

    public function getUsuarios() {
        echo json_encode(Usuario::getAll());
    }

    public function getMaestrosLista() {
        echo json_encode(Usuario::getMaestros());
    }

    public function crudUsuario() {
        $data = json_decode(file_get_contents('php://input'), true);
        $method = $_SERVER['REQUEST_METHOD'];

        try {
            if ($method === 'POST') {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                Usuario::create($data);
                echo json_encode(["message" => "Usuario creado"]);
            } elseif ($method === 'PUT') {
                if (!empty($data['password'])) {
                    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                }
                Usuario::update($data);
                echo json_encode(["message" => "Usuario actualizado"]);
            } elseif ($method === 'DELETE') {
                Usuario::delete($data['id']);
                echo json_encode(["message" => "Usuario eliminado"]);
            }
        } catch (Exception $e) {
            http_response_code(500); echo json_encode(["error" => $e->getMessage()]);
        }
    }

    public function getAllMaterias() {
        echo json_encode(Materia::getAll());
    }

    public function crudMateria() {
        $data = json_decode(file_get_contents('php://input'), true);
        $method = $_SERVER['REQUEST_METHOD'];
        $pdo = Database::pdo();

        try {
            if ($method === 'POST') {
                $stmt = $pdo->prepare("INSERT INTO materias (nombre, codigo, grupo, unidades, activo) VALUES (?, ?, ?, ?, 1)");
                $stmt->execute([$data['nombre'], $data['codigo'], $data['grupo'], $data['unidades']]);
                echo json_encode(["message" => "Materia creada"]);

            } elseif ($method === 'PUT') {
                $stmt = $pdo->prepare("UPDATE materias SET nombre=?, codigo=?, grupo=?, unidades=?, activo=? WHERE id=?");
                $stmt->execute([$data['nombre'], $data['codigo'], $data['grupo'], $data['unidades'], $data['activo'], $data['id']]);
                echo json_encode(["message" => "Materia actualizada"]);

            } elseif ($method === 'DELETE') {
                $stmt = $pdo->prepare("DELETE FROM materias WHERE id=?");
                $stmt->execute([$data['id']]);
                echo json_encode(["message" => "Materia eliminada"]);
            }
        } catch (Exception $e) {
            http_response_code(500); echo json_encode(["error" => $e->getMessage()]);
        }
    }
}