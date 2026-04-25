<?php
$pageTitle = 'Nuevo Pasanaku';
require __DIR__ . '/../layout/header.php';
?>

<div class="form-card">
  <div class="form-section-title">Datos del pasanaku</div>

  <?php if (!empty($error)): ?>
  <div class="flash-msg error mb-3">
    <i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars($error) ?>
  </div>
  <?php endif; ?>

  <form method="POST" action="?page=pasanaku&action=store">
    <div class="mb-3">
      <label class="form-label-sm">Nombre del grupo *</label>
      <input class="form-control-pk" type="text" name="nombre"
        value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>"
        placeholder="Ej: Pasanaku Oficina 2025" required autofocus>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-6">
        <label class="form-label-sm">Monto por ronda (Bs) *</label>
        <input class="form-control-pk" type="number" name="monto" min="1" step="0.01"
          value="<?= htmlspecialchars($_POST['monto'] ?? '') ?>"
          placeholder="500" required>
      </div>
      <div class="col-6">
        <label class="form-label-sm">Período</label>
        <select class="form-control-pk" name="periodo">
          <option value="semanal"   <?= ($_POST['periodo'] ?? '') === 'semanal'   ? 'selected' : '' ?>>Semanal</option>
          <option value="quincenal" <?= ($_POST['periodo'] ?? '') === 'quincenal' ? 'selected' : '' ?>>Quincenal</option>
          <option value="mensual"   <?= ($_POST['periodo'] ?? 'mensual') === 'mensual'   ? 'selected' : '' ?>>Mensual</option>
        </select>
      </div>
    </div>

    <div class="mb-4">
      <label class="form-label-sm">Fecha de inicio</label>
      <input class="form-control-pk" type="date" name="fecha_inicio"
        value="<?= htmlspecialchars($_POST['fecha_inicio'] ?? date('Y-m-d')) ?>">
    </div>

    <div class="d-flex gap-2">
      <a href="?page=dashboard" class="btn-pk-outline">Cancelar</a>
      <button class="btn-pk-primary" type="submit">
        <i class="bi bi-check-lg"></i> Crear pasanaku
      </button>
    </div>
  </form>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
