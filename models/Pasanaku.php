<?php
require_once __DIR__ . '/../config/database.php';

class Pasanaku {
    public static function all(): array {
        return getDB()->query('SELECT * FROM pasanakus ORDER BY created_at DESC')->fetchAll();
    }

    public static function activos(): array {
        return getDB()->query('SELECT * FROM pasanakus WHERE estado = "activo" ORDER BY created_at DESC')->fetchAll();
    }

    public static function finalizados(): array {
        return getDB()->query('SELECT * FROM pasanakus WHERE estado = "finalizado" ORDER BY created_at DESC')->fetchAll();
    }

    public static function find(int $id): array|false {
        $stmt = getDB()->prepare('SELECT * FROM pasanakus WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create(string $nombre, float $monto, string $periodo, string $fechaInicio): int {
        $stmt = getDB()->prepare(
            'INSERT INTO pasanakus (nombre, monto_contribucion, periodo, fecha_inicio) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$nombre, $monto, $periodo, $fechaInicio]);
        return (int)getDB()->lastInsertId();
    }

    public static function update(int $id, string $nombre, float $monto, string $periodo, string $fechaInicio): void {
        $stmt = getDB()->prepare(
            'UPDATE pasanakus SET nombre = ?, monto_contribucion = ?, periodo = ?, fecha_inicio = ? WHERE id = ?'
        );
        $stmt->execute([$nombre, $monto, $periodo, $fechaInicio, $id]);
    }

    public static function finalizar(int $id): void {
        $stmt = getDB()->prepare('UPDATE pasanakus SET estado = "finalizado" WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function getRondaActual(int $id): int {
        // Current round = last round with at least one payment, or 1
        $stmt = getDB()->prepare('SELECT MAX(ronda) FROM pagos WHERE pasanaku_id = ?');
        $stmt->execute([$id]);
        return (int)($stmt->fetchColumn() ?: 1);
    }
}
