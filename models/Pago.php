<?php
require_once __DIR__ . '/../config/database.php';

class Pago {
    public static function byPasanakuRonda(int $pasanakuId, int $ronda): array {
        $stmt = getDB()->prepare(
            'SELECT p.*, pp.persona_id, pe.nombre as persona_nombre
             FROM pagos p
             JOIN pasanaku_participantes pp ON pp.id = p.participante_id
             JOIN personas pe ON pe.id = pp.persona_id
             WHERE p.pasanaku_id = ? AND p.ronda = ?'
        );
        $stmt->execute([$pasanakuId, $ronda]);
        return $stmt->fetchAll();
    }

    public static function hasPagado(int $participanteId, int $ronda): bool {
        $stmt = getDB()->prepare(
            'SELECT COUNT(*) FROM pagos WHERE participante_id = ? AND ronda = ?'
        );
        $stmt->execute([$participanteId, $ronda]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function registrar(int $pasanakuId, int $participanteId, int $ronda, float $monto, ?string $fechaPago = null): void {
        $fecha = $fechaPago ?: date('Y-m-d');
        $stmt = getDB()->prepare(
            'INSERT INTO pagos (pasanaku_id, participante_id, ronda, monto, fecha_pago) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$pasanakuId, $participanteId, $ronda, $monto, $fecha]);
    }

    public static function eliminar(int $participanteId, int $ronda): void {
        $stmt = getDB()->prepare('DELETE FROM pagos WHERE participante_id = ? AND ronda = ?');
        $stmt->execute([$participanteId, $ronda]);
    }

    public static function countPagados(int $pasanakuId, int $ronda): int {
        $stmt = getDB()->prepare(
            'SELECT COUNT(*) FROM pagos WHERE pasanaku_id = ? AND ronda = ?'
        );
        $stmt->execute([$pasanakuId, $ronda]);
        return (int)$stmt->fetchColumn();
    }
}
