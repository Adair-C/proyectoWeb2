<?php
require_once __DIR__ . '/../Config/Database.php';

class Materia {
    public static function getAll() {
        return Database::pdo()->query("SELECT * FROM materias ORDER BY nombre ASC")->fetchAll();
    }

    public static function getByMaestro($id) {
        $sql = "SELECT m.* FROM materias m 
                JOIN asignacion_maestro_materia a ON m.id = a.materia_id 
                WHERE a.maestro_id = ? AND m.activo = 1 ORDER BY m.nombre";
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    public static function getGruposByMaestro($id) {
        $sql = "SELECT m.grupo, COUNT(*) as total FROM materias m 
                JOIN asignacion_maestro_materia a ON m.id = a.materia_id 
                WHERE a.maestro_id = ? GROUP BY m.grupo ORDER BY m.grupo";
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    public static function getDisponiblesAlumno($alumnoId) {
        $sql = "SELECT m.id, m.nombre, m.codigo, m.grupo, m.unidades, COALESCE(u.nombre_completo, 'Sin asignar') as maestro
                FROM materias m
                LEFT JOIN asignacion_maestro_materia amm ON m.id = amm.materia_id
                LEFT JOIN usuarios u ON amm.maestro_id = u.id
                WHERE m.activo = 1 AND m.id NOT IN (SELECT materia_id FROM inscripciones WHERE alumno_id = ?)
                ORDER BY m.nombre ASC";
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute([$alumnoId]);
        return $stmt->fetchAll();
    }

    public static function getInscritasAlumno($alumnoId) {
        $sql = "SELECT m.id, m.nombre, m.codigo, m.grupo, m.unidades, COALESCE(u.nombre_completo, 'Sin asignar') as maestro
                FROM inscripciones i
                JOIN materias m ON i.materia_id = m.id
                LEFT JOIN asignacion_maestro_materia amm ON m.id = amm.materia_id
                LEFT JOIN usuarios u ON amm.maestro_id = u.id
                WHERE i.alumno_id = ? ORDER BY m.nombre";
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute([$alumnoId]);
        return $stmt->fetchAll();
    }
    
    public static function create($data, $maestroId = null) {
        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO materias (nombre, codigo, grupo, unidades, activo) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$data['nombre'], $data['codigo'], $data['grupo'], $data['unidades'], $data['activo'] ?? 1]);
            $materiaId = $pdo->lastInsertId();

            if ($maestroId) {
                $stmt2 = $pdo->prepare("INSERT INTO asignacion_maestro_materia (maestro_id, materia_id) VALUES (?, ?)");
                $stmt2->execute([$maestroId, $materiaId]);
            }
            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
    
}