<?php
session_start();

// ── BASE URL ─────────────────────────────────────────────────────────────────
// Detect base URL dynamically (works on shared hosting and local Docker)
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
define('BASE_URL', ($scriptDir === '' ? '/' : $scriptDir . '/'));

// ── AUTOLOAD HELPERS ──────────────────────────────────────────────────────────
require_once __DIR__ . '/config/database.php';

// ── ROUTING ───────────────────────────────────────────────────────────────────
$page   = $_GET['page']   ?? 'login';
$action = $_GET['action'] ?? 'index';

// Public pages
$publicPages = ['login'];

// Protect all other pages
if (!in_array($page, $publicPages) && empty($_SESSION['admin_id'])) {
    header('Location: ?page=login');
    exit;
}

// Redirect logged-in users away from login
if ($page === 'login' && !empty($_SESSION['admin_id'])) {
    header('Location: ?page=dashboard');
    exit;
}

// ── DISPATCH ──────────────────────────────────────────────────────────────────
switch ($page) {

    // ── AUTH ─────────────────────────────────────────────────────────────────
    case 'login':
        require_once __DIR__ . '/controllers/AuthController.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || $action === 'post') {
            AuthController::login();
        } else {
            AuthController::showLogin();
        }
        break;

    case 'logout':
        require_once __DIR__ . '/controllers/AuthController.php';
        AuthController::logout();
        break;

    // ── DASHBOARD ────────────────────────────────────────────────────────────
    case 'dashboard':
        require_once __DIR__ . '/controllers/DashboardController.php';
        DashboardController::index();
        break;

    // ── PASANAKU ─────────────────────────────────────────────────────────────
    case 'pasanaku':
        require_once __DIR__ . '/controllers/PasanakuController.php';
        switch ($action) {
            case 'create':  PasanakuController::create();  break;
            case 'store':   PasanakuController::store();   break;
            case 'edit':    PasanakuController::edit();    break;
            case 'update':  PasanakuController::update();  break;
            case 'detail':  PasanakuController::detail();  break;
            case 'finalizar': PasanakuController::finalizar(); break;
            case 'togglePago':       PasanakuController::togglePago();       break;
            case 'reordenar':        PasanakuController::reordenar();        break;
            case 'registrarEntrega': PasanakuController::registrarEntrega(); break;
            case 'addParticipante':  PasanakuController::addParticipante();  break;
            case 'removeParticipante': PasanakuController::removeParticipante(); break;
            default:
                // List view — show active pasanakus
                require_once __DIR__ . '/models/Pasanaku.php';
                require_once __DIR__ . '/models/Participante.php';
                require_once __DIR__ . '/models/Pago.php';
                $pageTitle  = 'Mis Pasanakus';
                $pasanakus  = [];
                $raw = \Pasanaku::activos();
                foreach ($raw as &$pk) {
                    $pk['participantes'] = \Participante::byPasanaku($pk['id']);
                    $pk['ronda_actual']  = \Pasanaku::getRondaActual($pk['id']);
                    $pk['pagados_ronda'] = \Pago::countPagados($pk['id'], $pk['ronda_actual']);
                    $pk['receptor']      = null;
                    foreach ($pk['participantes'] as $p) {
                        if ((int)$p['orden'] === $pk['ronda_actual'] && $p['activo']) {
                            $pk['receptor'] = $p; break;
                        }
                    }
                    $pasanakus[] = $pk;
                }
                unset($pk);
                require __DIR__ . '/views/dashboard/index.php';
                break;
        }
        break;

    // ── PERSONAS ─────────────────────────────────────────────────────────────
    case 'personas':
        require_once __DIR__ . '/controllers/PersonaController.php';
        switch ($action) {
            case 'store':  PersonaController::store();  break;
            case 'update': PersonaController::update(); break;
            case 'delete': PersonaController::delete(); break;
            default:       PersonaController::index();  break;
        }
        break;

    // ── HISTORIAL ────────────────────────────────────────────────────────────
    case 'historial':
        require_once __DIR__ . '/controllers/AdminController.php';
        if ($action === 'detail') {
            AdminController::historialDetail();
        } else {
            AdminController::historial();
        }
        break;

    // ── PERFIL ───────────────────────────────────────────────────────────────
    case 'perfil':
        require_once __DIR__ . '/controllers/AdminController.php';
        switch ($action) {
            case 'updateEmail':    AdminController::updateEmail();    break;
            case 'updatePassword': AdminController::updatePassword(); break;
            default:               AdminController::perfil();         break;
        }
        break;

    // ── 404 ──────────────────────────────────────────────────────────────────
    default:
        header('Location: ?page=dashboard');
        exit;
}
