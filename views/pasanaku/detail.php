<?php
$pageTitle = htmlspecialchars($pasanaku['nombre']);
$activeParticipantes = array_filter($participantes, fn($p) => $p['activo']);
$totalActivos = count($activeParticipantes);
$receptorActual = null;
foreach ($activeParticipantes as $p) {
  if ((int)$p['orden'] === $ronda) { $receptorActual = $p; break; }
}
if (!$receptorActual && !empty($activeParticipantes)) {
  $receptorActual = reset($activeParticipantes);
}
$totalRonda = $pasanaku['monto_contribucion'] * $totalActivos;
$yaEntregado = $entregaRonda !== false;
require __DIR__ . '/../layout/header.php';
?>

<!-- Header row -->
<div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
  <div style="font-size:18px;font-weight:800"><?= htmlspecialchars($pasanaku['nombre']) ?></div>
  <span class="badge-green">
    <i class="bi bi-circle-fill" style="font-size:7px;margin-right:4px"></i>Activo
  </span>
  <span class="badge-amber ms-auto"><?= formatBs($pasanaku['monto_contribucion']) ?>/ronda</span>
  <a href="?page=pasanaku&action=edit&id=<?= $pasanaku['id'] ?>" class="btn-icon" title="Editar">
    <i class="bi bi-pencil"></i>
  </a>
</div>

<!-- Ronda tabs -->
<div class="round-tabs">
<?php for ($r = 1; $r <= $totalRondas; $r++): ?>
  <?php $entregadaEsta = in_array($r, $entregadasRondas); ?>
  <a href="?page=pasanaku&action=detail&id=<?= $pasanaku['id'] ?>&ronda=<?= $r ?>"
    class="round-tab <?= $ronda === $r ? 'active' : '' ?> <?= $entregadaEsta ? 'entregada' : '' ?>">
    <?php if ($entregadaEsta): ?>
      <i class="bi bi-check-lg" style="color:var(--pk-green)"></i>
    <?php endif; ?>
    Ronda <?= $r ?>
  </a>
<?php endfor; ?>
</div>

<!-- 3-column grid -->
<div class="detail-grid">

  <!-- LEFT: Participantes -->
  <div class="detail-panel">
    <div class="panel-header">
      <span><i class="bi bi-people"></i>Participantes</span>
      <div class="d-flex align-items-center gap-2">
        <span style="font-size:12px;color:var(--pk-muted);font-weight:500"><?= $totalActivos ?> personas</span>
        <button class="btn-icon" onclick="openAddParticipante()" title="Agregar participante">
          <i class="bi bi-person-plus"></i>
        </button>
      </div>
    </div>

    <div id="sortable-participants" data-pasanaku-id="<?= $pasanaku['id'] ?>">
    <?php foreach ($participantes as $idx => $p):
      $esReceptorRonda = ((int)$p['orden'] === $ronda);
    ?>
      <div class="participant-item <?= !$p['activo'] ? 'participant-inactive' : '' ?>"
        data-part-id="<?= $p['id'] ?>">
        <i class="bi bi-grip-vertical drag-handle <?= !$p['activo'] ? 'd-none' : '' ?>"></i>
        <div class="p-num"><?= $p['orden'] ?></div>
        <div style="flex:1;min-width:0">
          <div class="p-name"><?= htmlspecialchars($p['nombre']) ?></div>
          <?php if (!$p['activo']): ?>
            <div class="p-date" style="color:var(--pk-danger)">Turno saltado</div>
          <?php endif; ?>
        </div>
        <?php if ($esReceptorRonda && $p['activo']): ?>
          <span class="badge-amber" style="font-size:10px">Esta ronda</span>
        <?php endif; ?>
        <?php if ($p['activo']): ?>
        <form method="POST" action="?page=pasanaku&action=removeParticipante"
          onsubmit="return confirm('¿Desactivar a <?= htmlspecialchars(addslashes($p['nombre'])) ?>? Su turno quedará saltado.')">
          <input type="hidden" name="participante_id" value="<?= $p['id'] ?>">
          <input type="hidden" name="pasanaku_id" value="<?= $pasanaku['id'] ?>">
          <button class="btn-icon danger" type="submit" title="Desactivar turno">
            <i class="bi bi-x-lg"></i>
          </button>
        </form>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
    </div>
  </div>

  <!-- CENTER: Payment grid -->
  <div class="detail-panel">
    <div class="panel-header">
      <span><i class="bi bi-grid-3x3-gap"></i>Estado de pagos — Ronda <?= $ronda ?></span>
      <span style="font-size:12px;font-weight:500;color:var(--pk-muted)">
        <span id="pagados-count"><?= $pagadosCount ?></span>/<span id="total-count"><?= $totalActivos ?></span> pagaron
      </span>
    </div>
    <div class="payment-grid-wrap">
    <?php foreach ($activeParticipantes as $p):
      $esReceptor = ((int)$p['orden'] === $ronda);
      $pagoInfo   = $pagadosMap[$p['id']] ?? null;
      $pagado     = !empty($pagoInfo);
      $fechaPago  = $pagoInfo['fecha'] ?? null;
    ?>
      <div class="payment-row">
        <div style="flex:1;min-width:0">
          <div class="payment-row-name" title="<?= htmlspecialchars($p['nombre']) ?>">
            <?= htmlspecialchars($p['nombre']) ?>
          </div>
          <div class="payment-date" id="fecha-<?= $p['id'] ?>" style="font-size:10px;color:var(--pk-muted);<?= $pagado ? '' : 'display:none' ?>">
            <?= $fechaPago ? date('d/m/Y', strtotime($fechaPago)) : '' ?>
          </div>
        </div>
        <button class="pay-cell <?= $esReceptor ? ($pagado ? 'pay-paid' : 'pay-recipient') : ($pagado ? 'pay-paid' : 'pay-pending') ?>"
          data-part-id="<?= $p['id'] ?>"
          data-ronda="<?= $ronda ?>"
          data-pasanaku-id="<?= $pasanaku['id'] ?>"
          data-recipient="<?= $esReceptor ? '1' : '0' ?>"
          title="<?= $esReceptor ? 'Receptor de esta ronda' : '' ?><?= $pagado ? ' — Pagó ✓ (clic para revertir)' : ' — Pendiente (clic para marcar pagado)' ?>">
          <?php if ($esReceptor && !$pagado): ?>
            <i class="bi bi-star-fill" style="font-size:12px"></i>
          <?php elseif ($pagado): ?>
            <i class="bi bi-check-lg"></i>
          <?php else: ?>
            <i class="bi bi-clock"></i>
          <?php endif; ?>
        </button>
      </div>
    <?php endforeach; ?>

    <hr class="divider">
    <div class="d-flex justify-content-between align-items-center" style="font-size:13px">
      <span style="color:var(--pk-muted)">Recaudado</span>
      <strong style="color:var(--pk-green)" id="recaudado-amount">
        <?= formatBs($pagadosCount * $pasanaku['monto_contribucion']) ?>
      </strong>
    </div>
    <div class="d-flex justify-content-between align-items-center mt-1" style="font-size:13px">
      <span style="color:var(--pk-muted)">Total ronda</span>
      <strong><?= formatBs($totalRonda) ?></strong>
    </div>
    <div class="progress-bar-wrap mt-2">
      <div class="progress-bar-fill" id="pagados-pct"
        style="width:<?= $totalActivos > 0 ? round(($pagadosCount/$totalActivos)*100) : 0 ?>%">
      </div>
    </div>

    <input type="hidden" id="monto-base" value="<?= $pasanaku['monto_contribucion'] ?>">
    </div>
  </div>

  <!-- RIGHT: Delivery panel -->
  <div class="detail-panel">
    <div class="panel-header">
      <span><i class="bi bi-cash-coin"></i>Entrega</span>
    </div>
    <div class="delivery-panel">
      <div class="form-label-sm">Receptor — Ronda <?= $ronda ?></div>

      <?php if ($receptorActual): ?>
      <div class="recipient-badge">
        <div class="recipient-avatar"><?= initials($receptorActual['nombre']) ?></div>
        <div>
          <div class="recipient-name"><?= htmlspecialchars($receptorActual['nombre']) ?></div>
          <div class="recipient-sub">Ronda <?= $ronda ?></div>
        </div>
      </div>
      <?php else: ?>
      <div class="recipient-badge" style="opacity:0.6">
        <div class="recipient-avatar">?</div>
        <div><div class="recipient-name">Sin receptor asignado</div></div>
      </div>
      <?php endif; ?>

      <div class="form-label-sm">Monto a entregar</div>
      <div class="amount-display"><?= formatBs($totalRonda) ?></div>

      <div class="form-label-sm mb-2">Progreso de pagos</div>
      <div class="d-flex gap-2 mb-3 flex-wrap">
        <span class="badge-green" id="entrega-badge-pagaron">
          <i class="bi bi-check"></i><span id="entrega-pagaron-count"><?= $pagadosCount ?></span> pagaron
        </span>
        <span class="badge-amber" id="entrega-badge-pendientes">
          <i class="bi bi-clock"></i><span id="entrega-pendientes-count"><?= $totalActivos - $pagadosCount ?></span> pendientes
        </span>
      </div>

      <?php if ($yaEntregado): ?>
      <div class="entrega-done-box">
        <i class="bi bi-check-circle-fill" style="color:var(--pk-green);font-size:18px"></i>
        <div>
          <div style="font-size:13px;font-weight:700;color:var(--pk-green)">Entrega registrada</div>
          <div style="font-size:11px;color:var(--pk-muted)">
            <?= $entregaRonda['fecha_entrega'] ?? '' ?>
          </div>
        </div>
      </div>
      <?php else: ?>
      <button class="btn-pk-primary w-100 justify-content-center" id="btn-registrar-entrega"
        onclick="confirmarEntrega()"
        <?= $pagadosCount < $totalActivos ? 'disabled' : '' ?>>
        <i class="bi bi-cash-coin"></i>
        Registrar entrega
      </button>
      <div id="entrega-hint" style="font-size:11px;color:var(--pk-muted);margin-top:8px;text-align:center;<?= $pagadosCount >= $totalActivos ? 'display:none' : '' ?>">
        Faltan <span id="entrega-hint-count"><?= $totalActivos - $pagadosCount ?></span> pagos para habilitar
      </div>
      <?php endif; ?>

      <hr class="divider">
      <div class="form-label-sm mb-2">Notas de la ronda</div>
      <textarea class="form-control-pk" rows="2" placeholder="Observaciones opcionales…"></textarea>
    </div>
  </div>
</div>

<!-- Modal: Registrar fecha de pago -->
<div class="modal-overlay d-none" id="modal-fecha-pago">
  <div class="modal-box" style="max-width:360px">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div class="modal-title mb-0">Registrar pago</div>
      <button class="btn-icon" onclick="cancelarPago()"><i class="bi bi-x-lg"></i></button>
    </div>
    <p style="font-size:13px;color:var(--pk-muted);margin-bottom:16px">
      Confirma el pago de <strong id="pago-nombre-label" style="color:var(--pk-text)"></strong>
    </p>
    <label class="form-label-sm">Fecha de pago</label>
    <input class="form-control-pk mt-1 mb-4" type="date" id="pago-fecha-input"
      value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>">
    <div class="d-flex gap-2 justify-content-end">
      <button class="btn-pk-outline" onclick="cancelarPago()">Cancelar</button>
      <button class="btn-pk-primary" onclick="confirmarPago()">
        <i class="bi bi-check-lg"></i> Confirmar pago
      </button>
    </div>
  </div>
</div>

<!-- Modal: Confirmar entrega -->
<?php if ($receptorActual && !$yaEntregado): ?>
<div class="modal-overlay d-none" id="modal-entrega">
  <div class="modal-box">
    <div class="modal-title">Confirmar entrega</div>
    <p style="font-size:14px;color:var(--pk-muted)">
      ¿Confirmas la entrega de
      <strong style="color:var(--pk-green)"><?= formatBs($totalRonda) ?></strong>
      a <strong><?= htmlspecialchars($receptorActual['nombre']) ?></strong>
      correspondiente a la Ronda <?= $ronda ?>?
    </p>
    <div class="d-flex gap-2 justify-content-end mt-3">
      <button class="btn-pk-outline" onclick="cancelarEntrega()">Cancelar</button>
      <button class="btn-pk-primary"
        onclick="submitEntrega(<?= $pasanaku['id'] ?>, <?= $receptorActual['id'] ?>, <?= $ronda ?>)">
        <i class="bi bi-check-lg"></i>Confirmar
      </button>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Modal: Agregar participante -->
<div class="modal-overlay d-none" id="modal-add-participante">
  <div class="modal-box">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div class="modal-title mb-0">Agregar participantes</div>
      <button class="btn-icon" onclick="closeModal('modal-add-participante')"><i class="bi bi-x-lg"></i></button>
    </div>
    <?php
    $partPersonaIds = array_column($participantes, 'persona_id');
    $disponibles = array_filter($personas, fn($p) => !in_array($p['id'], $partPersonaIds));
    ?>
    <?php if (empty($disponibles)): ?>
      <p style="font-size:13px;color:var(--pk-muted);text-align:center;padding:16px 0">
        Todas las personas del directorio ya están en este pasanaku.
      </p>
      <div class="d-flex justify-content-end">
        <button type="button" class="btn-pk-outline" onclick="closeModal('modal-add-participante')">Cerrar</button>
      </div>
    <?php else: ?>
    <form method="POST" action="?page=pasanaku&action=addParticipante">
      <input type="hidden" name="pasanaku_id" value="<?= $pasanaku['id'] ?>">
      <div class="form-label-sm mb-2">Selecciona una o más personas</div>
      <div style="max-height:260px;overflow-y:auto;border:1.5px solid var(--pk-border);border-radius:9px;padding:4px">
        <?php foreach ($disponibles as $persona): ?>
        <label style="display:flex;align-items:center;gap:10px;padding:8px 10px;cursor:pointer;border-radius:7px;font-size:13px;transition:background 0.1s"
          onmouseover="this.style.background='var(--pk-surface)'" onmouseout="this.style.background=''">
          <input type="checkbox" name="persona_ids[]" value="<?= $persona['id'] ?>"
            style="width:16px;height:16px;accent-color:var(--pk-green);flex-shrink:0"
            onchange="updateAddCount()">
          <div class="user-avatar" style="width:28px;height:28px;font-size:11px;flex-shrink:0">
            <?= initials($persona['nombre']) ?>
          </div>
          <span style="font-weight:600"><?= htmlspecialchars($persona['nombre']) ?></span>
          <?php if ($persona['telefono']): ?>
          <span style="color:var(--pk-muted);font-size:11px;margin-left:auto"><?= htmlspecialchars($persona['telefono']) ?></span>
          <?php endif; ?>
        </label>
        <?php endforeach; ?>
      </div>
      <div style="font-size:12px;color:var(--pk-muted);margin-top:8px" id="add-count-label">
        Ninguna seleccionada
      </div>
      <div class="d-flex gap-2 justify-content-end mt-3">
        <button type="button" class="btn-pk-outline" onclick="closeModal('modal-add-participante')">Cancelar</button>
        <button type="submit" class="btn-pk-primary" id="btn-add-submit" disabled>
          <i class="bi bi-person-plus"></i> <span id="btn-add-label">Agregar</span>
        </button>
      </div>
    </form>
    <?php endif; ?>
  </div>
</div>

<script>
function updateAddCount() {
  const checked = document.querySelectorAll('#modal-add-participante input[type=checkbox]:checked');
  const n = checked.length;
  document.getElementById('add-count-label').textContent =
    n === 0 ? 'Ninguna seleccionada' : n === 1 ? '1 persona seleccionada' : n + ' personas seleccionadas';
  document.getElementById('btn-add-submit').disabled = n === 0;
  document.getElementById('btn-add-label').textContent = n > 1 ? 'Agregar ' + n : 'Agregar';
}
</script>

<!-- Finalizar pasanaku -->
<?php
$todasEntregadas = count($entregadasRondas) >= $totalRondas && $totalRondas > 0;
if ($todasEntregadas && $pasanaku['estado'] === 'activo'):
?>
<div class="mt-4 p-3" style="background:var(--pk-amber-pale);border:1px solid #FDE68A;border-radius:12px">
  <div style="font-size:14px;font-weight:700;margin-bottom:8px">
    <i class="bi bi-trophy me-2" style="color:var(--pk-amber-dark)"></i>
    ¡Todas las rondas completadas!
  </div>
  <p style="font-size:13px;color:var(--pk-muted);margin-bottom:12px">
    Puedes finalizar este pasanaku y moverlo al historial.
  </p>
  <form method="POST" action="?page=pasanaku&action=finalizar"
    onsubmit="return confirm('¿Finalizar y archivar este pasanaku?')">
    <input type="hidden" name="id" value="<?= $pasanaku['id'] ?>">
    <button class="btn-pk-primary" type="submit">
      <i class="bi bi-archive"></i> Finalizar pasanaku
    </button>
  </form>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
