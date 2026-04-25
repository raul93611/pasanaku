<?php
require_once __DIR__ . '/../config/database.php';

class Persona {
    public static function all(): array {
        return getDB()->query('SELECT * FROM personas ORDER BY nombre')->fetchAll();
    }

    public static function find(int $id): array|false {
        $stmt = getDB()->prepare('SELECT * FROM personas WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create(string $nombre, ?string $telefono): int {
        $stmt = getDB()->prepare('INSERT INTO personas (nombre, telefono) VALUES (?, ?)');
        $stmt->execute([$nombre, $telefono ?: null]);
        return (int)getDB()->lastInsertId();
    }

    public static function update(int $id, string $nombre, ?string $telefono): void {
        $stmt = getDB()->prepare('UPDATE personas SET nombre = ?, telefono = ? WHERE id = ?');
        $stmt->execute([$nombre, $telefono ?: null, $id]);
    }

    public static function delete(int $id): void {
        $stmt = getDB()->prepare('DELETE FROM personas WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function isActiveInPasanaku(int $id): bool {
        $stmt = getDB()->prepare(
            'SELECT COUNT(*) FROM pasanaku_participantes pp
             JOIN pasanakus p ON p.id = pp.pasanaku_id
             WHERE pp.persona_id = ? AND pp.activo = 1 AND p.estado = "activo"'
        );
        $stmt->execute([$id]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function activeGroupCount(int $id): int {
        $stmt = getDB()->prepare(
            'SELECT COUNT(*) FROM pasanaku_participantes pp
             JOIN pasanakus p ON p.id = pp.pasanaku_id
             WHERE pp.persona_id = ? AND pp.activo = 1 AND p.estado = "activo"'
        );
        $stmt->execute([$id]);
        return (int)$stmt->fetchColumn();
    }
}
