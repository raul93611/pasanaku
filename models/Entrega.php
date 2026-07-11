<?php
require_once __DIR__ . '/../config/database.php';

class Entrega {
    public static function byPasanaku(int $pasanakuId): array {
        $stmt = getDB()->prepare('SELECT * FROM entregas WHERE pasanaku_id = ?');
        $stmt->execute([$pasanakuId]);
        return $stmt->fetchAll();
    }

    public static function getRonda(int $pasanakuId, int $ronda): array|false {
        $stmt = getDB()->prepare(
            'SELECT e.*, pe.nombre as persona_nombre
             FROM entregas e
             JOIN pasanaku_participantes pp ON pp.id = e.participante_id
             JOIN personas pe ON pe.id = pp.persona_id
             WHERE e.pasanaku_id = ? AND e.ronda = ? LIMIT 1'
        );
        $stmt->execute([$pasanakuId, $ronda]);
        return $stmt->fetch();
    }

    public static function registrar(int $pasanakuId, int $participanteId, int $ronda, ?string $notas = null): void {
        $stmt = getDB()->prepare(
            'INSERT INTO entregas (pasanaku_id, participante_id, ronda, fecha_entrega, notas) VALUES (?, ?, ?, CURDATE(), ?)'
        );
        $stmt->execute([$pasanakuId, $participanteId, $ronda, $notas]);
    }

    public static function countByPasanaku(int $pasanakuId): int {
        $stmt = getDB()->prepare('SELECT COUNT(*) FROM entregas WHERE pasanaku_id = ?');
        $stmt->execute([$pasanakuId]);
        return (int)$stmt->fetchColumn();
    }
}
