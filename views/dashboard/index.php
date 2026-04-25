<?php
$pageTitle = 'Dashboard';
$uniqueParticipantes = [];
foreach ($pasanakus as $pk) {
  foreach ($pk['participantes'] as $p) { $uniqueParticipantes[$p['persona_id']] = true; }
}
$totalFondo = array_sum(array_map(fn($pk) => $pk['monto_contribucion'] * count($pk['participantes']), $pasanakus));
require __DIR__ . '/../layout/header.php';
?>

<!-- Stat pills -->
<div class="stat-row">
  <div class="stat-pill">
    <div class="stat-pill-val"><?= count($pasanakus) ?></div>
    <div class="stat-pill-label">Pasanakus activos</div>
  </div>
  <div class="stat-pill">
    <div class="stat-pill-val"><?= count($uniqueParticipantes) ?></div>
    <div class="stat-pill-label">Participantes únicos</div>
  </div>
  <div class="stat-pill">
    <div class="stat-pill-val" style="color:var(--pk-amber-dark)"><?= formatBs($totalFondo) ?></div>
    <div class="stat-pill-label">Fondos en circulación</div>
  </div>
</div>

<?php if (empty($pasanakus)): ?>
<div class="empty-state">
  <i class="bi bi-people"></i>
  <p>No tienes pasanakus activos.<br>
  <a href="?page=pasanaku&action=create" class="btn-pk-primary mt-3 d-inline-flex">
    <i class="bi bi-plus-lg"></i> Crear primer pasanaku
  </a></p>
</div>
<?php else: ?>

<div style="font-size:13px;font-weight:700;color:var(--pk-muted);margin-bottom:12px;letter-spacing:0.5px;text-transform:uppercase">
  Grupos activos
</div>

<div class="row g-3">
<?php foreach ($pasanakus as $pk):
  $n       = count($pk['participantes']);
  $pagados = $pk['pagados_ronda'];
  $pct     = $n > 0 ? round(($pagados / $n) * 100) : 0;
  $receptor = $pk['receptor'];
?>
  <div class="col-12 col-sm-6 col-xl-4">
    <a href="?page=pasanaku&action=detail&id=<?= $pk['id'] ?>" class="pasanaku-card">
      <div class="pk-card-header">
        <div class="pk-card-name"><?= htmlspecialchars($pk['nombre']) ?></div>
        <div class="pk-card-amount"><?= formatBs($pk['monto_contribucion']) ?></div>
      </div>
      <div class="pk-card-round">
        <span class="status-dot dot-active"></span>
        Ronda <?= $pk['ronda_actual'] ?> de <?= $n ?> ·
        <span style="text-transform:capitalize"><?= $pk['periodo'] ?></span>
      </div>
      <div class="pk-card-next">
        Próximo receptor:
        <strong><?= $receptor ? htmlspecialchars($receptor['nombre']) : '—' ?></strong>
      </div>
      <div class="progress-bar-wrap">
        <div class="progress-bar-fill" style="width:<?= $pct ?>%"></div>
      </div>
      <div class="progress-label">
        <span><strong><?= $pagados ?>/<?= $n ?></strong> pagaron</span>
        <span><?= $pct ?>%</span>
      </div>
    </a>
  </div>
<?php endforeach; ?>
</div>

<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
