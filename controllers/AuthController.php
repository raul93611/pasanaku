<?php
require_once __DIR__ . '/../models/Admin.php';

class AuthController {
    public static function showLogin(): void {
        require __DIR__ . '/../views/auth/login.php';
    }

    public static function login(): void {
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';

        $admin = Admin::findByEmail($email);
        if ($admin && password_verify($pass, $admin['password_hash'])) {
            $_SESSION['admin_id']    = $admin['id'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_nombre'] = $admin['nombre'];
            header('Location: ?page=dashboard');
            exit;
        }

        $error = 'Correo o contraseña incorrectos.';
        require __DIR__ . '/../views/auth/login.php';
    }

    public static function logout(): void {
        session_destroy();
        header('Location: ?page=login');
        exit;
    }
}
