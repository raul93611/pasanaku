<?php
require_once __DIR__ . '/../config/database.php';

class Admin {
    public static function findByEmail(string $email): array|false {
        $stmt = getDB()->prepare('SELECT * FROM admins WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function updateEmail(int $id, string $email): void {
        $stmt = getDB()->prepare('UPDATE admins SET email = ? WHERE id = ?');
        $stmt->execute([$email, $id]);
    }

    public static function updatePassword(int $id, string $hash): void {
        $stmt = getDB()->prepare('UPDATE admins SET password_hash = ? WHERE id = ?');
        $stmt->execute([$hash, $id]);
    }

    public static function find(int $id): array|false {
        $stmt = getDB()->prepare('SELECT * FROM admins WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
