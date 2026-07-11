<?php
$pageTitle = htmlspecialchars($pasanaku['nombre']);
$totalRondas    = $numRondas;
$totalRecaudado = $pasanaku['monto_contribucion'] * $totalRondas * count($participantes);
require __DIR__ . '/../layout/header.php';
?>

<!-- Back + header -->
<div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
  <a href="?page=historial" class="btn-icon"><i class="bi bi-arrow-left"></i></a>
  <div style="font-size:18px;font-weight:800"><?= htmlspecialchars($pasanaku['nombre']) ?></div>
  <span class="badge-red ms-1"><i class="bi bi-archive me-1"></i>Finalizado</span>
</div>

<!-- Summary pills -->
<div class="stat-row mb-4">
  <div class="stat-pill">
    <div class="stat-pill-val"><?= count($participantes) ?></div>
    <div class="stat-pill-label">Participantes</div>
  </div>
  <div class="stat-pill">
    <div class="stat-pill-val"><?= $totalRondas ?></div>
    <div class="stat-pill-label">Rondas completadas</div>
  </div>
  <div class="stat-pill">
    <div class="stat-pill-val"><?= formatBs($pasanaku['monto_contribucion']) ?></div>
    <div class="stat-pill-label">Aporte por ronda</div>
  </div>
  <div class="stat-pill">
    <div class="stat-pill-val" style="color:var(--pk-amber-dark)"><?= formatBs($totalRecaudado) ?></div>
    <div class="stat-pill-label">Total movilizado</div>
  </div>
</div>

<!-- Info row -->
<div class="detail-panel mb-4" style="max-width:560px">
  <div class="panel-header"><span><i class="bi bi-info-circle"></i>Datos del pasanaku</span></div>
  <div style="padding:16px;display:grid;grid-template-columns:1fr 1fr;gap:12px">
    <div>
      <div class="form-label-sm">Período</div>
      <div style="font-size:14px;font-weight:600;text-transform:capitalize"><?= $pasanaku['periodo'] ?></div>
    </div>
    <div>
      <div class="form-label-sm">Fecha de inicio</div>
      <div style="font-size:14px;font-weight:600"><?= $pasanaku['fecha_inicio'] ?></div>
    </div>
  </div>
</div>

<!-- Round-by-round accordion -->
<?php for ($r = 1; $r <= $totalRondas; $r++):
  $receptor = null;
  foreach ($participantes as $p) {
    if ((int)$p['orden'] === $r && $p['activo']) { $receptor = $p; break; }
  }
  $entrega      = $entregasMap[$r] ?? null;
  $pagadosRonda = array_filter($participantes, fn($p) => !empty($pagosMap[$r][$p['id']]));
  $pagadosCount = count($pagadosRonda);
  $totalParts   = count($participantes);
?>
<div class="detail-panel mb-3">
  <div class="panel-header" style="cursor:pointer" onclick="toggleRonda(<?= $r ?>)">
    <span>
      <?php if ($entrega): ?>
        <i class="bi bi-check-circle-fill" style="color:var(--pk-green);margin-right:6px"></i>
      <?php else: ?>
        <i class="bi bi-circle" style="color:var(--pk-muted);margin-right:6px"></i>
      <?php endif; ?>
      Ronda <?= $r ?>
      <?php if ($receptor): ?>
        — <span style="color:var(--pk-green);font-weight:700"><?= htmlspecialchars($receptor['nombre']) ?></span>
      <?php endif; ?>
    </span>
    <div class="d-flex align-items-center gap-2">
      <?php if ($entrega): ?>
        <span class="badge-green">Entregado <?= $entrega['fecha_entrega'] ?></span>
      <?php endif; ?>
      <span style="font-size:12px;color:var(--pk-muted);font-weight:500"><?= $pagadosCount ?>/<?= $totalParts ?> pagaron</span>
      <i class="bi bi-chevron-down" id="chevron-<?= $r ?>" style="color:var(--pk-muted);font-size:12px;transition:transform 0.2s"></i>
    </div>
  </div>

  <div id="ronda-body-<?= $r ?>" style="display:none">
    <div style="padding:12px 16px">

      <!-- Payment rows -->
      <?php foreach ($participantes as $p):
        if (!$p['activo']) continue;
        $esReceptor = ((int)$p['orden'] === $r);
        $pagado     = !empty($pagosMap[$r][$p['id']]);
      ?>
      <div class="payment-row" style="margin-bottom:8px">
        <div class="d-flex align-items-center gap-2" style="flex:1;min-width:0">
          <div class="user-avatar" style="width:26px;height:26px;font-size:10px;flex-shrink:0">
            <?= initials($p['nombre']) ?>
          </div>
          <span class="payment-row-name"><?= htmlspecialchars($p['nombre']) ?></span>
          <?php if ($esReceptor): ?>
            <span class="badge-amber" style="font-size:10px">Receptor</span>
          <?php endif; ?>
        </div>
        <div class="pay-cell <?= $pagado ? 'pay-paid' : 'pay-na' ?>" style="cursor:default" title="<?= $pagado ? 'Pagó' : 'No pagó' ?>">
          <?php if ($pagado): ?>
            <i class="bi bi-check-lg"></i>
          <?php else: ?>
            <i class="bi bi-x-lg" style="font-size:11px"></i>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>

      <!-- Delivery summary -->
      <?php if ($entrega): ?>
      <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--pk-border);display:flex;align-items:center;gap:10px">
        <i class="bi bi-cash-coin" style="color:var(--pk-green)"></i>
        <span style="font-size:13px;color:var(--pk-muted)">Entregado el <?= $entrega['fecha_entrega'] ?></span>
        <strong style="color:var(--pk-green);margin-left:auto"><?= formatBs($pasanaku['monto_contribucion'] * $totalParts) ?></strong>
      </div>
      <?php if (!empty($entrega['notas'])): ?>
      <div style="margin-top:8px;display:flex;gap:8px;align-items:flex-start">
        <i class="bi bi-sticky" style="color:var(--pk-muted);font-size:13px;margin-top:2px"></i>
        <span style="font-size:12px;color:var(--pk-text);white-space:pre-wrap"><?= htmlspecialchars($entrega['notas']) ?></span>
      </div>
      <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php endfor; ?>

<script>
function toggleRonda(r) {
  const body    = document.getElementById('ronda-body-' + r);
  const chevron = document.getElementById('chevron-' + r);
  const open    = body.style.display !== 'none';
  body.style.display    = open ? 'none' : 'block';
  chevron.style.transform = open ? '' : 'rotate(180deg)';
}
// Open first round by default
document.addEventListener('DOMContentLoaded', () => toggleRonda(1));
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
