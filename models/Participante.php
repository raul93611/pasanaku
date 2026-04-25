<?php
require_once __DIR__ . '/../config/database.php';

class Participante {
    public static function byPasanaku(int $pasanakuId): array {
        $stmt = getDB()->prepare(
            'SELECT pp.*, pe.nombre, pe.telefono
             FROM pasanaku_participantes pp
             JOIN personas pe ON pe.id = pp.persona_id
             WHERE pp.pasanaku_id = ?
             ORDER BY pp.orden ASC'
        );
        $stmt->execute([$pasanakuId]);
        return $stmt->fetchAll();
    }

    public static function find(int $id): array|false {
        $stmt = getDB()->prepare(
            'SELECT pp.*, pe.nombre, pe.telefono
             FROM pasanaku_participantes pp
             JOIN personas pe ON pe.id = pp.persona_id
             WHERE pp.id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function add(int $pasanakuId, int $personaId, int $orden): int {
        $stmt = getDB()->prepare(
            'INSERT INTO pasanaku_participantes (pasanaku_id, persona_id, orden) VALUES (?, ?, ?)'
        );
        $stmt->execute([$pasanakuId, $personaId, $orden]);
        return (int)getDB()->lastInsertId();
    }

    public static function deactivate(int $id): void {
        $stmt = getDB()->prepare('UPDATE pasanaku_participantes SET activo = 0 WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function updateOrden(int $id, int $orden): void {
        $stmt = getDB()->prepare('UPDATE pasanaku_participantes SET orden = ? WHERE id = ?');
        $stmt->execute([$orden, $id]);
    }

    public static function maxOrden(int $pasanakuId): int {
        $stmt = getDB()->prepare('SELECT MAX(orden) FROM pasanaku_participantes WHERE pasanaku_id = ?');
        $stmt->execute([$pasanakuId]);
        return (int)($stmt->fetchColumn() ?: 0);
    }

    public static function isPersonaInPasanaku(int $pasanakuId, int $personaId): bool {
        $stmt = getDB()->prepare(
            'SELECT COUNT(*) FROM pasanaku_participantes WHERE pasanaku_id = ? AND persona_id = ? AND activo = 1'
        );
        $stmt->execute([$pasanakuId, $personaId]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
