<?php
$pageTitle = 'Editar — ' . htmlspecialchars($pasanaku['nombre']);
require __DIR__ . '/../layout/header.php';
?>

<div class="d-flex align-items-center gap-2 mb-4">
  <a href="?page=pasanaku&action=detail&id=<?= $pasanaku['id'] ?>" class="btn-icon">
    <i class="bi bi-arrow-left"></i>
  </a>
  <h5 class="mb-0 fw-bold">Editar pasanaku</h5>
</div>

<div class="form-card">
  <?php if (!empty($error)): ?>
  <div class="flash-msg error mb-3">
    <i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars($error) ?>
  </div>
  <?php endif; ?>

  <form method="POST" action="?page=pasanaku&action=update">
    <input type="hidden" name="id" value="<?= $pasanaku['id'] ?>">

    <div class="mb-3">
      <label class="form-label-sm">Nombre del grupo *</label>
      <input class="form-control-pk" type="text" name="nombre"
        value="<?= htmlspecialchars($_POST['nombre'] ?? $pasanaku['nombre']) ?>" required>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-6">
        <label class="form-label-sm">Monto por ronda (Bs) *</label>
        <input class="form-control-pk" type="number" name="monto" min="1" step="0.01"
          value="<?= htmlspecialchars($_POST['monto'] ?? $pasanaku['monto_contribucion']) ?>" required>
      </div>
      <div class="col-6">
        <label class="form-label-sm">Período</label>
        <select class="form-control-pk" name="periodo">
          <?php foreach (['semanal','quincenal','mensual'] as $p): ?>
          <option value="<?= $p ?>" <?= ($pasanaku['periodo'] === $p) ? 'selected' : '' ?>>
            <?= ucfirst($p) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="mb-4">
      <label class="form-label-sm">Fecha de inicio</label>
      <input class="form-control-pk" type="date" name="fecha_inicio"
        value="<?= htmlspecialchars($_POST['fecha_inicio'] ?? $pasanaku['fecha_inicio']) ?>">
    </div>

    <div class="d-flex gap-2">
      <a href="?page=pasanaku&action=detail&id=<?= $pasanaku['id'] ?>" class="btn-pk-outline">Cancelar</a>
      <button class="btn-pk-primary" type="submit">
        <i class="bi bi-check-lg"></i> Guardar cambios
      </button>
    </div>
  </form>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
