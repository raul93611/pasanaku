<?php
$pageTitle = 'Gestión de Personas';
require __DIR__ . '/../layout/header.php';
?>

<div class="d-flex gap-2 mb-3 flex-wrap align-items-center">
  <div class="search-wrap flex-grow-1" style="max-width:340px">
    <i class="bi bi-search"></i>
    <input class="form-control-pk" id="persona-search"
      placeholder="Buscar persona…" oninput="filterPersonas(this.value)">
  </div>
</div>

<div class="table-card">
  <table id="personas-table">
    <thead>
      <tr>
        <th>Nombre</th>
        <th class="d-none d-md-table-cell">Teléfono</th>
        <th>Grupos activos</th>
        <th style="width:90px"></th>
      </tr>
    </thead>
    <tbody>
    <?php if (empty($personas)): ?>
      <tr>
        <td colspan="4">
          <div class="empty-state">
            <i class="bi bi-person-slash"></i>
            <p>No hay personas registradas aún.</p>
          </div>
        </td>
      </tr>
    <?php else: ?>
    <?php foreach ($personas as $p): ?>
      <tr class="persona-row" data-nombre="<?= htmlspecialchars(strtolower($p['nombre'])) ?>">
        <td>
          <div class="d-flex align-items-center gap-2">
            <div class="user-avatar" style="width:30px;height:30px;font-size:11px">
              <?= initials($p['nombre']) ?>
            </div>
            <span style="font-weight:600"><?= htmlspecialchars($p['nombre']) ?></span>
          </div>
        </td>
        <td class="d-none d-md-table-cell" style="color:var(--pk-muted)">
          <?= htmlspecialchars($p['telefono'] ?? '—') ?>
        </td>
        <td>
          <span class="<?= $p['grupos_activos'] > 0 ? 'badge-green' : 'badge-amber' ?>">
            <?= $p['grupos_activos'] ?> <?= $p['grupos_activos'] === 1 ? 'grupo' : 'grupos' ?>
          </span>
        </td>
        <td>
          <div class="d-flex gap-1 justify-content-end">
            <button class="btn-icon" title="Editar"
              onclick="openEditPersona(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['nombre'])) ?>', '<?= htmlspecialchars(addslashes($p['telefono'] ?? '')) ?>')">
              <i class="bi bi-pencil"></i>
            </button>
            <button class="btn-icon danger" title="Eliminar"
              onclick="openDeletePersona(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['nombre'])) ?>')">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Modal: Nueva persona -->
<div class="modal-overlay d-none" id="modal-nueva-persona">
  <div class="modal-box">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div class="modal-title mb-0">Nueva persona</div>
      <button class="btn-icon" onclick="closeModal('modal-nueva-persona')"><i class="bi bi-x-lg"></i></button>
    </div>
    <form method="POST" action="?page=personas&action=store">
      <div class="mb-3">
        <label class="form-label-sm">Nombre completo *</label>
        <input class="form-control-pk" type="text" name="nombre" placeholder="Nombre y apellido" required autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label-sm">Teléfono</label>
        <input class="form-control-pk" type="text" name="telefono" placeholder="7XXXXXXX">
      </div>
      <div class="d-flex gap-2 justify-content-end">
        <button type="button" class="btn-pk-outline" onclick="closeModal('modal-nueva-persona')">Cancelar</button>
        <button type="submit" class="btn-pk-primary">
          <i class="bi bi-check-lg"></i> Agregar
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Editar persona -->
<div class="modal-overlay d-none" id="modal-edit-persona">
  <div class="modal-box">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div class="modal-title mb-0">Editar persona</div>
      <button class="btn-icon" onclick="closeModal('modal-edit-persona')"><i class="bi bi-x-lg"></i></button>
    </div>
    <form method="POST" action="?page=personas&action=update">
      <input type="hidden" name="id">
      <div class="mb-3">
        <label class="form-label-sm">Nombre completo *</label>
        <input class="form-control-pk" type="text" name="nombre" placeholder="Nombre y apellido" required>
      </div>
      <div class="mb-3">
        <label class="form-label-sm">Teléfono</label>
        <input class="form-control-pk" type="text" name="telefono" placeholder="7XXXXXXX">
      </div>
      <div class="d-flex gap-2 justify-content-end">
        <button type="button" class="btn-pk-outline" onclick="closeModal('modal-edit-persona')">Cancelar</button>
        <button type="submit" class="btn-pk-primary">
          <i class="bi bi-check-lg"></i> Guardar cambios
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Eliminar persona -->
<div class="modal-overlay d-none" id="modal-delete-persona">
  <div class="modal-box">
    <div class="modal-title">Eliminar persona</div>
    <p style="font-size:14px;color:var(--pk-muted)">
      ¿Eliminar a <strong class="delete-persona-name"></strong> del directorio?
      Esta acción no se puede deshacer.
    </p>
    <form method="POST" action="?page=personas&action=delete">
      <input type="hidden" name="id">
      <div class="d-flex gap-2 justify-content-end mt-3">
        <button type="button" class="btn-pk-outline" onclick="closeModal('modal-delete-persona')">Cancelar</button>
        <button type="submit" class="btn-pk-danger">
          <i class="bi bi-trash"></i> Eliminar
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function filterPersonas(q) {
  const rows = document.querySelectorAll('.persona-row');
  const lq = q.toLowerCase();
  rows.forEach(row => {
    row.style.display = row.dataset.nombre.includes(lq) ? '' : 'none';
  });
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
