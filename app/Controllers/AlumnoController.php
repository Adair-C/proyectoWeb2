<?php
require_once __DIR__ . '/../Models/Materia.php';
require_once __DIR__ . '/../Models/Calificacion.php';
require_once __DIR__ . '/../Helpers/Auth.php';

class AlumnoController {
    public function __construct() {
        if (!Auth::isLoggedIn() || Auth::rol() !== 'alumno') {
            http_response_code(403); exit(json_encode(["error" => "No autorizado"]));
        }
    }

    public function getInscritas() {
        echo json_encode(Materia::getInscritasAlumno(Auth::userId()));
    }

    public function getDisponibles() {
        echo json_encode(Materia::getDisponiblesAlumno(Auth::userId()));
    }

    public function getNotas() {
        $materias = Materia::getInscritasAlumno(Auth::userId());
        $notas = Calificacion::getByAlumno(Auth::userId());
        echo json_encode(["materias" => $materias, "notas" => $notas]);
    }

    public function inscribir() {
        $data = json_decode(file_get_contents('php://input'), true);
        $materiaId = $data['materia_id'] ?? 0;
        $alumnoId = Auth::userId();
        $limite = 7;

        $pdo = Database::pdo();
        
        // Verificar límite
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM inscripciones WHERE alumno_id = ?");
        $stmt->execute([$alumnoId]);
        if ($stmt->fetchColumn() >= $limite) {
            echo json_encode(["error" => "Límite de materias alcanzado ($limite)."]);
            return;
        }

        // Verificar duplicado
        $check = $pdo->prepare("SELECT id FROM inscripciones WHERE alumno_id = ? AND materia_id = ?");
        $check->execute([$alumnoId, $materiaId]);
        if ($check->fetch()) {
            echo json_encode(["error" => "Ya estás inscrito."]);
            return;
        }

        // Inscribir
        $ins = $pdo->prepare("INSERT INTO inscripciones (alumno_id, materia_id) VALUES (?, ?)");
        if ($ins->execute([$alumnoId, $materiaId])) {
            echo json_encode(["message" => "Inscripción exitosa"]);
        } else {
            echo json_encode(["error" => "Error al inscribir"]);
        }
    }
}