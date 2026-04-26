<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'Pasanaku') ?> — Gestor de Ahorros</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>assets/css/app.css" rel="stylesheet">
</head>
<body>

<?php
$currentPage   = $_GET['page']   ?? 'dashboard';
$currentAction = $_GET['action'] ?? 'index';
$currentPkId   = $_GET['id']     ?? null;
$adminNombre   = $_SESSION['admin_nombre'] ?? 'Admin';
$adminEmail    = $_SESSION['admin_email']  ?? '';

function initials(string $name): string {
  $parts = explode(' ', trim($name));
  $ini = '';
  foreach (array_slice($parts, 0, 2) as $p) { $ini .= mb_strtoupper(mb_substr($p, 0, 1)); }
  return $ini ?: '?';
}
function formatBs(float $n): string { return 'Bs ' . number_format($n, 0, ',', '.'); }
?>

<!-- MOBILE TOPBAR -->
<div class="mobile-topbar">
  <div class="mobile-brand">
    <div class="brand-icon" style="width:28px;height:28px;font-size:14px;border-radius:8px">
      <i class="bi bi-coin"></i>
    </div>
    Pasanaku
  </div>
  <div class="d-flex align-items-center gap-2">
    <?php if ($currentPage === 'dashboard' || ($currentPage === 'pasanaku' && $currentAction === '')): ?>
      <a href="?page=pasanaku&action=create" class="btn-pk-primary" style="font-size:12px;padding:6px 10px">
        <i class="bi bi-plus-lg"></i> Nuevo
      </a>
    <?php elseif ($currentPage === 'personas'): ?>
      <button class="btn-pk-primary" style="font-size:12px;padding:6px 10px" onclick="openModal('modal-nueva-persona')">
        <i class="bi bi-person-plus"></i>
      </button>
    <?php endif; ?>
    <button class="btn-icon" id="sidebar-toggle"
      style="background:transparent;border:none;color:#fff;font-size:20px">
      <i class="bi bi-list"></i>
    </button>
  </div>
</div>

<div class="sidebar-overlay"></div>

<div class="app-shell">
  <!-- SIDEBAR -->
  <nav class="sidebar">
    <div class="sidebar-brand">
      <div class="brand-name">
        <div class="brand-icon"><i class="bi bi-coin"></i></div>
        Pasanaku
      </div>
    </div>

    <div class="sidebar-nav">
      <div class="nav-section-label">Principal</div>

      <a href="?page=dashboard" class="nav-item-btn <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
        <i class="bi bi-grid"></i> Dashboard
      </a>
      <a href="?page=pasanaku" class="nav-item-btn <?= ($currentPage === 'pasanaku' && $currentAction !== 'detail') ? 'active' : '' ?>">
        <i class="bi bi-people-fill"></i> Mis Pasanakus
      </a>
      <a href="?page=personas" class="nav-item-btn <?= $currentPage === 'personas' ? 'active' : '' ?>">
        <i class="bi bi-person-lines-fill"></i> Personas
      </a>
      <a href="?page=historial" class="nav-item-btn <?= $currentPage === 'historial' ? 'active' : '' ?>">
        <i class="bi bi-clock-history"></i> Historial
      </a>

      <?php
      require_once __DIR__ . '/../../models/Pasanaku.php';
      $navPasanakus = Pasanaku::activos();
      if ($navPasanakus):
      ?>
      <div class="nav-section-label" style="margin-top:8px">Grupos activos</div>
      <?php foreach ($navPasanakus as $navPk): ?>
      <a href="?page=pasanaku&action=detail&id=<?= $navPk['id'] ?>"
        class="nav-item-btn <?= ($currentPage === 'pasanaku' && $currentAction === 'detail' && (int)$currentPkId === $navPk['id']) ? 'active' : '' ?>">
        <i class="bi bi-collection"></i>
        <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($navPk['nombre']) ?></span>
      </a>
      <?php endforeach; ?>
      <?php endif; ?>

      <div style="padding:12px 20px 0">
        <a href="?page=pasanaku&action=create" class="btn-pk-outline btn-pk-outline-sidebar w-100 justify-content-center" style="font-size:12px">
          <i class="bi bi-plus-circle"></i> Nuevo pasanaku
        </a>
      </div>
    </div>

    <div class="sidebar-footer">
      <div class="user-chip">
        <div class="user-avatar"><?= initials($adminNombre) ?></div>
        <div style="min-width:0">
          <div style="font-weight:600;font-size:13px"><?= htmlspecialchars($adminNombre) ?></div>
          <div style="font-size:11px;opacity:0.7;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($adminEmail) ?></div>
        </div>
        <div class="d-flex gap-1 ms-auto">
          <a href="?page=perfil" class="btn-icon" style="background:transparent;border:none;color:rgba(255,255,255,0.5)" title="Perfil">
            <i class="bi bi-person-gear"></i>
          </a>
          <a href="?page=logout" class="btn-icon" style="background:transparent;border:none;color:rgba(255,255,255,0.5)" title="Cerrar sesión">
            <i class="bi bi-box-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>
  </nav>

  <!-- MAIN CONTENT -->
  <div class="main-content">
    <!-- TOPBAR -->
    <div class="topbar">
      <div class="topbar-title">
        <?php if (($currentPage === 'pasanaku' && $currentAction === 'detail') || ($currentPage === 'historial' && $currentAction === 'detail')): ?>
          <a href="?page=<?= $currentPage ?>" class="btn-icon me-1"><i class="bi bi-arrow-left"></i></a>
        <?php endif; ?>
        <?= htmlspecialchars($pageTitle ?? 'Dashboard') ?>
      </div>
      <div class="topbar-actions">
        <?php if ($currentPage === 'dashboard'): ?>
          <a href="?page=pasanaku&action=create" class="btn-pk-primary">
            <i class="bi bi-plus-lg"></i> Nuevo
          </a>
        <?php elseif ($currentPage === 'personas'): ?>
          <button class="btn-pk-primary" onclick="openModal('modal-nueva-persona')">
            <i class="bi bi-person-plus"></i> Nueva persona
          </button>
        <?php endif; ?>
      </div>
    </div>

    <!-- FLASH MESSAGES -->
    <div style="padding:0 24px">
    <?php if (!empty($_SESSION['flash'])): ?>
      <div class="flash-msg success mt-3">
        <i class="bi bi-check-circle-fill"></i>
        <?= htmlspecialchars($_SESSION['flash']) ?>
      </div>
      <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
      <div class="flash-msg error mt-3">
        <i class="bi bi-exclamation-circle-fill"></i>
        <?= htmlspecialchars($_SESSION['flash_error']) ?>
      </div>
      <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
    </div>

    <div class="page-body screen-enter">
