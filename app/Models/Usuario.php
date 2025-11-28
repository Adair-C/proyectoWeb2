<?php
require_once __DIR__ . '/../Config/Database.php';

class Usuario {
    public static function findByUsernameOrEmail($valor) {
        $stmt = Database::pdo()->prepare("SELECT * FROM usuarios WHERE username = ? OR email = ? LIMIT 1");
        $stmt->execute([$valor, $valor]);
        return $stmt->fetch();
    }
    
    public static function getAll() {
        return Database::pdo()->query("SELECT id, username, nombre_completo, email, rol, activo FROM usuarios ORDER BY id DESC")->fetchAll();
    }

    public static function getMaestros() {
        return Database::pdo()->query("SELECT id, nombre_completo FROM usuarios WHERE rol = 'maestro' AND activo = 1 ORDER BY nombre_completo")->fetchAll();
    }

    public static function create($data) {
        $stmt = Database::pdo()->prepare("INSERT INTO usuarios (username, password, nombre_completo, email, rol, activo) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$data['username'], $data['password'], $data['nombre'], $data['email'], $data['rol'], 1]);
    }

    public static function update($data) {
        $sql = "UPDATE usuarios SET username=?, nombre_completo=?, email=?, rol=?, activo=?";
        $params = [$data['username'], $data['nombre'], $data['email'], $data['rol'], $data['activo']];
        
        if (!empty($data['password'])) {
            $sql .= ", password=?";
            $params[] = $data['password'];
        }
        $sql .= " WHERE id=?";
        $params[] = $data['id'];

        $stmt = Database::pdo()->prepare($sql);
        return $stmt->execute($params);
    }

    public static function delete($id) {
        $stmt = Database::pdo()->prepare("DELETE FROM usuarios WHERE id = ?");
        return $stmt->execute([$id]);
    }
}