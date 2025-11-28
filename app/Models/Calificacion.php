<?php
require_once __DIR__ . '/../Config/Database.php';

class Calificacion {
    public static function guardar($datos) {
        $sql = "INSERT INTO calificaciones (alumno_id, materia_id, maestro_id, unidad, calificacion) 
                VALUES (?, ?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE calificacion = VALUES(calificacion), maestro_id = VALUES(maestro_id)";
        $stmt = Database::pdo()->prepare($sql);
        return $stmt->execute([
            $datos['alumno_id'], 
            $datos['materia_id'], 
            $datos['maestro_id'], 
            $datos['unidad'], 
            $datos['calificacion']
        ]);
    }

    public static function getByMateria($materiaId) {
        $stmt = Database::pdo()->prepare("SELECT alumno_id, unidad, calificacion FROM calificaciones WHERE materia_id = ?");
        $stmt->execute([$materiaId]);
        $raw = $stmt->fetchAll();
        
        $notas = [];
        foreach($raw as $r) {
            $notas[$r['alumno_id']][$r['unidad']] = $r['calificacion'];
        }
        return $notas;
    }

    public static function getByAlumno($alumnoId) {
        $stmt = Database::pdo()->prepare("SELECT materia_id, unidad, calificacion FROM calificaciones WHERE alumno_id = ?");
        $stmt->execute([$alumnoId]);
        $raw = $stmt->fetchAll();

        $notas = [];
        foreach($raw as $r) {
            $notas[$r['materia_id']][$r['unidad']] = $r['calificacion'];
        }
        return $notas;
    }
}