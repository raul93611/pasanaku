<?php
$pageTitle = 'Historial';
$totalMov = array_sum(array_column($pasanakus, 'total_recaudado'));
require __DIR__ . '/../layout/header.php';
?>

<div class="stat-row mb-4">
  <div class="stat-pill">
    <div class="stat-pill-val"><?= count($pasanakus) ?></div>
    <div class="stat-pill-label">Pasanakus completados</div>
  </div>
  <div class="stat-pill">
    <div class="stat-pill-val" style="color:var(--pk-amber-dark)"><?= formatBs($totalMov) ?></div>
    <div class="stat-pill-label">Total movilizado</div>
  </div>
</div>

<?php if (empty($pasanakus)): ?>
<div class="empty-state">
  <i class="bi bi-archive"></i>
  <p>No hay pasanakus finalizados aún.</p>
</div>
<?php else: ?>

<?php foreach ($pasanakus as $pk): ?>
<div class="historial-item">
  <div class="h-icon"><i class="bi bi-archive"></i></div>
  <div style="flex:1">
    <div class="h-name"><?= htmlspecialchars($pk['nombre']) ?></div>
    <div class="h-meta">
      <?= $pk['num_participantes'] ?> participantes ·
      <?= $pk['num_rondas'] ?> rondas ·
      <?= $pk['fecha_inicio'] ?> — cerrado
    </div>
  </div>
  <div>
    <div class="h-amount"><?= formatBs($pk['total_recaudado']) ?></div>
    <div class="h-rounds"><?= formatBs($pk['monto_contribucion']) ?>/persona</div>
  </div>
</div>
<?php endforeach; ?>

<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
