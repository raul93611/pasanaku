<?php
require_once __DIR__ . '/../models/Pasanaku.php';
require_once __DIR__ . '/../models/Participante.php';
require_once __DIR__ . '/../models/Pago.php';
require_once __DIR__ . '/../models/Entrega.php';
require_once __DIR__ . '/../models/Persona.php';

class PasanakuController {

    // ── CREATE ─────────────────────────────────────────────────────────────────
    public static function create(): void {
        require __DIR__ . '/../views/pasanaku/create.php';
    }

    public static function store(): void {
        $nombre      = trim($_POST['nombre'] ?? '');
        $monto       = (float)($_POST['monto'] ?? 0);
        $periodo     = $_POST['periodo'] ?? 'mensual';
        $fechaInicio = $_POST['fecha_inicio'] ?? date('Y-m-d');

        if (!$nombre || $monto <= 0) {
            $error = 'Nombre y monto son requeridos.';
            require __DIR__ . '/../views/pasanaku/create.php';
            return;
        }

        $id = Pasanaku::create($nombre, $monto, $periodo, $fechaInicio);
        $_SESSION['flash'] = 'Pasanaku creado exitosamente.';
        header("Location: ?page=pasanaku&action=detail&id=$id");
        exit;
    }

    // ── EDIT ───────────────────────────────────────────────────────────────────
    public static function edit(): void {
        $id = (int)($_GET['id'] ?? 0);
        $pasanaku = Pasanaku::find($id);
        if (!$pasanaku) { header('Location: ?page=dashboard'); exit; }
        require __DIR__ . '/../views/pasanaku/edit.php';
    }

    public static function update(): void {
        $id          = (int)($_POST['id'] ?? 0);
        $nombre      = trim($_POST['nombre'] ?? '');
        $monto       = (float)($_POST['monto'] ?? 0);
        $periodo     = $_POST['periodo'] ?? 'mensual';
        $fechaInicio = $_POST['fecha_inicio'] ?? date('Y-m-d');

        if (!$nombre || $monto <= 0) {
            $error = 'Nombre y monto son requeridos.';
            $pasanaku = Pasanaku::find($id);
            require __DIR__ . '/../views/pasanaku/edit.php';
            return;
        }

        Pasanaku::update($id, $nombre, $monto, $periodo, $fechaInicio);
        $_SESSION['flash'] = 'Pasanaku actualizado.';
        header("Location: ?page=pasanaku&action=detail&id=$id");
        exit;
    }

    // ── DETAIL ─────────────────────────────────────────────────────────────────
    public static function detail(): void {
        $id = (int)($_GET['id'] ?? 0);
        $pasanaku = Pasanaku::find($id);
        if (!$pasanaku) { header('Location: ?page=dashboard'); exit; }

        $participantes = Participante::byPasanaku($id);
        $rondaActual   = Pasanaku::getRondaActual($id);
        $ronda         = (int)($_GET['ronda'] ?? $rondaActual);
        $totalRondas   = count(array_filter($participantes, fn($p) => $p['activo']));

        // Payment status map: participante_id => bool
        $pagosRonda = Pago::byPasanakuRonda($id, $ronda);
        $pagadosMap = [];
        foreach ($pagosRonda as $p) { $pagadosMap[$p['participante_id']] = true; }

        // Entrega info
        $entregaRonda = Entrega::getRonda($id, $ronda);
        $entregasAll  = Entrega::byPasanaku($id);
        $entregadasRondas = array_column($entregasAll, 'ronda');

        $pagadosCount = count($pagosRonda);
        $personas = Persona::all();

        require __DIR__ . '/../views/pasanaku/detail.php';
    }

    // ── FINALIZAR ──────────────────────────────────────────────────────────────
    public static function finalizar(): void {
        $id = (int)($_POST['id'] ?? 0);
        Pasanaku::finalizar($id);
        $_SESSION['flash'] = 'Pasanaku finalizado y movido al historial.';
        header('Location: ?page=historial');
        exit;
    }

    // ── AJAX: Toggle Pago ──────────────────────────────────────────────────────
    public static function togglePago(): void {
        header('Content-Type: application/json');
        $participanteId = (int)($_POST['participante_id'] ?? 0);
        $ronda          = (int)($_POST['ronda'] ?? 0);
        $pasanakuId     = (int)($_POST['pasanaku_id'] ?? 0);

        if (!$participanteId || !$ronda || !$pasanakuId) {
            echo json_encode(['ok' => false]); exit;
        }

        $pasanaku = Pasanaku::find($pasanakuId);

        if (Pago::hasPagado($participanteId, $ronda)) {
            Pago::eliminar($participanteId, $ronda);
            $pagado = false;
        } else {
            Pago::registrar($pasanakuId, $participanteId, $ronda, $pasanaku['monto_contribucion']);
            $pagado = true;
        }

        $pagadosCount = Pago::countPagados($pasanakuId, $ronda);
        echo json_encode(['ok' => true, 'pagado' => $pagado, 'pagados_count' => $pagadosCount]);
        exit;
    }

    // ── AJAX: Reorder Participantes ────────────────────────────────────────────
    public static function reordenar(): void {
        header('Content-Type: application/json');
        $orden = $_POST['orden'] ?? [];
        if (!is_array($orden)) { echo json_encode(['ok' => false]); exit; }

        foreach ($orden as $pos => $partId) {
            Participante::updateOrden((int)$partId, (int)$pos + 1);
        }
        echo json_encode(['ok' => true]);
        exit;
    }

    // ── AJAX: Registrar Entrega ────────────────────────────────────────────────
    public static function registrarEntrega(): void {
        header('Content-Type: application/json');
        $pasanakuId     = (int)($_POST['pasanaku_id'] ?? 0);
        $participanteId = (int)($_POST['participante_id'] ?? 0);
        $ronda          = (int)($_POST['ronda'] ?? 0);

        if (!$pasanakuId || !$participanteId || !$ronda) {
            echo json_encode(['ok' => false, 'msg' => 'Datos incompletos']); exit;
        }

        // Check if already delivered
        if (Entrega::getRonda($pasanakuId, $ronda)) {
            echo json_encode(['ok' => false, 'msg' => 'Ya registrada']); exit;
        }

        Entrega::registrar($pasanakuId, $participanteId, $ronda);
        echo json_encode(['ok' => true]);
        exit;
    }

    // ── Add Participante ───────────────────────────────────────────────────────
    public static function addParticipante(): void {
        $pasanakuId = (int)($_POST['pasanaku_id'] ?? 0);
        $personaId  = (int)($_POST['persona_id'] ?? 0);

        if ($pasanakuId && $personaId && !Participante::isPersonaInPasanaku($pasanakuId, $personaId)) {
            $orden = Participante::maxOrden($pasanakuId) + 1;
            Participante::add($pasanakuId, $personaId, $orden);
            $_SESSION['flash'] = 'Participante agregado.';
        }
        header("Location: ?page=pasanaku&action=detail&id=$pasanakuId");
        exit;
    }

    // ── Remove Participante ────────────────────────────────────────────────────
    public static function removeParticipante(): void {
        $participanteId = (int)($_POST['participante_id'] ?? 0);
        $pasanakuId     = (int)($_POST['pasanaku_id'] ?? 0);

        if ($participanteId) {
            Participante::deactivate($participanteId);
            $_SESSION['flash'] = 'Participante desactivado (turno saltado).';
        }
        header("Location: ?page=pasanaku&action=detail&id=$pasanakuId");
        exit;
    }
}
