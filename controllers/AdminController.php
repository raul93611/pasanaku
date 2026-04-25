<?php
require_once __DIR__ . '/../models/Admin.php';

class AdminController {
    public static function perfil(): void {
        $admin = Admin::find($_SESSION['admin_id']);
        require __DIR__ . '/../views/admin/perfil.php';
    }

    public static function updateEmail(): void {
        $email = trim($_POST['email'] ?? '');
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'Correo inválido.';
            header('Location: ?page=perfil');
            exit;
        }
        Admin::updateEmail($_SESSION['admin_id'], $email);
        $_SESSION['admin_email'] = $email;
        $_SESSION['flash'] = 'Correo actualizado.';
        header('Location: ?page=perfil');
        exit;
    }

    public static function updatePassword(): void {
        $current = $_POST['password_actual'] ?? '';
        $new     = $_POST['password_nuevo'] ?? '';
        $confirm = $_POST['password_confirmar'] ?? '';

        $admin = Admin::find($_SESSION['admin_id']);

        if (!password_verify($current, $admin['password_hash'])) {
            $_SESSION['flash_error'] = 'Contraseña actual incorrecta.';
            header('Location: ?page=perfil');
            exit;
        }
        if (strlen($new) < 6) {
            $_SESSION['flash_error'] = 'La nueva contraseña debe tener al menos 6 caracteres.';
            header('Location: ?page=perfil');
            exit;
        }
        if ($new !== $confirm) {
            $_SESSION['flash_error'] = 'Las contraseñas no coinciden.';
            header('Location: ?page=perfil');
            exit;
        }

        Admin::updatePassword($_SESSION['admin_id'], password_hash($new, PASSWORD_BCRYPT));
        $_SESSION['flash'] = 'Contraseña actualizada.';
        header('Location: ?page=perfil');
        exit;
    }

    public static function historial(): void {
        require_once __DIR__ . '/../models/Pasanaku.php';
        require_once __DIR__ . '/../models/Participante.php';
        require_once __DIR__ . '/../models/Entrega.php';

        $pasanakus = Pasanaku::finalizados();
        foreach ($pasanakus as &$pk) {
            $pk['participantes']     = Participante::byPasanaku($pk['id']);
            $pk['num_participantes'] = count($pk['participantes']);
            $pk['num_rondas']        = $pk['num_participantes'];
            $pk['total_recaudado']   = $pk['monto_contribucion'] * $pk['num_rondas'] * $pk['num_participantes'];
        }
        unset($pk);
        require __DIR__ . '/../views/historial/index.php';
    }

    public static function historialDetail(): void {
        require_once __DIR__ . '/../models/Pasanaku.php';
        require_once __DIR__ . '/../models/Participante.php';
        require_once __DIR__ . '/../models/Pago.php';
        require_once __DIR__ . '/../models/Entrega.php';

        $id       = (int)($_GET['id'] ?? 0);
        $pasanaku = Pasanaku::find($id);
        if (!$pasanaku || $pasanaku['estado'] !== 'finalizado') {
            header('Location: ?page=historial'); exit;
        }

        $participantes = Participante::byPasanaku($id);
        $numRondas     = count($participantes);

        // Build payment map: [ronda][participante_id] = true
        $pagosMap = [];
        for ($r = 1; $r <= $numRondas; $r++) {
            $pagos = Pago::byPasanakuRonda($id, $r);
            foreach ($pagos as $p) {
                $pagosMap[$r][$p['participante_id']] = true;
            }
        }

        // Build delivery map: [ronda] => entrega row
        $entregasMap = [];
        $entregas    = Entrega::byPasanaku($id);
        foreach ($entregas as $e) {
            $entregasMap[$e['ronda']] = $e;
        }

        require __DIR__ . '/../views/historial/detail.php';
    }
}
