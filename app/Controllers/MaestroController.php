<?php
require_once __DIR__ . '/../Models/Materia.php';
require_once __DIR__ . '/../Models/Calificacion.php';
require_once __DIR__ . '/../Helpers/Auth.php';

class MaestroController {
    public function __construct() {
        if (!Auth::isLoggedIn() || Auth::rol() !== 'maestro') {
            http_response_code(403); exit(json_encode(["error" => "No autorizado"]));
        }
    }

    public function getMaterias() {
        echo json_encode(Materia::getByMaestro(Auth::userId()));
    }

    public function getGrupos() {
        echo json_encode(Materia::getGruposByMaestro(Auth::userId()));
    }

    public function getCalificacionesTabla() {
        $materiaId = $_GET['materia_id'] ?? 0;
        $pdo = Database::pdo();

        // Info Materia
        $stmt = $pdo->prepare("SELECT nombre, grupo, unidades FROM materias WHERE id = ?");
        $stmt->execute([$materiaId]);
        $materia = $stmt->fetch();

        // Alumnos
        $stmt = $pdo->prepare("SELECT u.id, u.nombre_completo FROM usuarios u JOIN inscripciones i ON u.id = i.alumno_id WHERE i.materia_id = ? AND u.rol = 'alumno' ORDER BY u.nombre_completo");
        $stmt->execute([$materiaId]);
        $alumnos = $stmt->fetchAll();

        // Notas
        $notas = Calificacion::getByMateria($materiaId);

        echo json_encode(["materia" => $materia, "alumnos" => $alumnos, "notas" => $notas]);
    }

    public function guardarCalificacion() {
        $data = json_decode(file_get_contents('php://input'), true);
        $data['maestro_id'] = Auth::userId();
        if (Calificacion::guardar($data)) {
            echo json_encode(["message" => "Guardado"]);
        } else {
            http_response_code(500); echo json_encode(["error" => "Error al guardar"]);
        }
    }

    public function materiaCrud() {
        $data = json_decode(file_get_contents('php://input'), true);
        $method = $_SERVER['REQUEST_METHOD'];
        
        try {
            if ($method === 'POST') {
                // El maestro crea materia y se asigna a sí mismo
                Materia::create($data, Auth::userId());
                echo json_encode(["message" => "Materia creada"]);

            } elseif ($method === 'PUT') {
                // Validar propiedad
                $pdo = Database::pdo();
                $check = $pdo->prepare("SELECT id FROM asignacion_maestro_materia WHERE maestro_id=? AND materia_id=?");
                $check->execute([Auth::userId(), $data['id']]);
                if(!$check->fetch()) throw new Exception("No tienes permiso.");

                $stmt = $pdo->prepare("UPDATE materias SET nombre=?, codigo=?, grupo=?, unidades=? WHERE id=?");
                $stmt->execute([$data['nombre'], $data['codigo'], $data['grupo'], $data['unidades'], $data['id']]);
                echo json_encode(["message" => "Actualizada"]);

            } elseif ($method === 'DELETE') {
                // Validar propiedad
                $pdo = Database::pdo();
                $check = $pdo->prepare("SELECT id FROM asignacion_maestro_materia WHERE maestro_id=? AND materia_id=?");
                $check->execute([Auth::userId(), $data['id']]);
                if(!$check->fetch()) throw new Exception("No tienes permiso.");

                $stmt = $pdo->prepare("DELETE FROM materias WHERE id=?");
                $stmt->execute([$data['id']]);
                echo json_encode(["message" => "Eliminada"]);
            }
        } catch (Exception $e) {
            http_response_code(500); echo json_encode(["error" => $e->getMessage()]);
        }
    }

    public function getAlumnosDisponibles() {
        $materiaId = $_GET['materia_id'];
        $pdo = Database::pdo();
        
        // Nombre materia
        $stmtMat = $pdo->prepare("SELECT nombre FROM materias WHERE id = ?");
        $stmtMat->execute([$materiaId]);
        $nombre = $stmtMat->fetchColumn();

        // Inscritos
        $stmtIn = $pdo->prepare("SELECT u.id, u.nombre_completo as nombre FROM usuarios u JOIN inscripciones i ON u.id = i.alumno_id WHERE i.materia_id = ? AND u.rol='alumno'");
        $stmtIn->execute([$materiaId]);
        $inscritos = $stmtIn->fetchAll();

        // Disponibles
        $stmtDis = $pdo->prepare("SELECT id, nombre_completo as nombre FROM usuarios WHERE rol='alumno' AND activo=1 AND id NOT IN (SELECT alumno_id FROM inscripciones WHERE materia_id=?)");
        $stmtDis->execute([$materiaId]);
        $disponibles = $stmtDis->fetchAll();

        echo json_encode(["materia" => $nombre, "inscritos" => $inscritos, "disponibles" => $disponibles]);
    }

    public function gestionarInscripcion() {
        $data = json_decode(file_get_contents('php://input'), true);
        $pdo = Database::pdo();
        
        if ($data['accion'] === 'inscribir') {
            $stmt = $pdo->prepare("INSERT INTO inscripciones (materia_id, alumno_id) VALUES (?, ?)");
            $stmt->execute([$data['materia_id'], $data['alumno_id']]);
            echo json_encode(["message" => "Alumno inscrito"]);
        } else {
            $stmt = $pdo->prepare("DELETE FROM inscripciones WHERE materia_id=? AND alumno_id=?");
            $stmt->execute([$data['materia_id'], $data['alumno_id']]);
            echo json_encode(["message" => "Alumno eliminado"]);
        }
    }
}