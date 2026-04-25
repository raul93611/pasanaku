<?php
$pageTitle = 'Perfil';
require __DIR__ . '/../layout/header.php';
?>

<div class="row g-4" style="max-width:640px">
  <!-- Email -->
  <div class="col-12">
    <div class="form-card">
      <div class="form-section-title">Información de acceso</div>
      <form method="POST" action="?page=perfil&action=updateEmail">
        <div class="mb-3">
          <label class="form-label-sm">Correo electrónico</label>
          <input class="form-control-pk" type="email" name="email"
            value="<?= htmlspecialchars($admin['email']) ?>" required>
        </div>
        <button class="btn-pk-primary" type="submit">
          <i class="bi bi-check-lg"></i> Guardar correo
        </button>
      </form>
    </div>
  </div>

  <!-- Password -->
  <div class="col-12">
    <div class="form-card">
      <div class="form-section-title">Cambiar contraseña</div>
      <form method="POST" action="?page=perfil&action=updatePassword">
        <div class="mb-3">
          <label class="form-label-sm">Contraseña actual</label>
          <input class="form-control-pk" type="password" name="password_actual"
            placeholder="Contraseña actual" required>
        </div>
        <div class="mb-3">
          <label class="form-label-sm">Nueva contraseña</label>
          <input class="form-control-pk" type="password" name="password_nuevo"
            placeholder="Mínimo 6 caracteres" required minlength="6">
        </div>
        <div class="mb-4">
          <label class="form-label-sm">Confirmar nueva contraseña</label>
          <input class="form-control-pk" type="password" name="password_confirmar"
            placeholder="Repetir contraseña" required>
        </div>
        <button class="btn-pk-primary" type="submit">
          <i class="bi bi-shield-lock"></i> Cambiar contraseña
        </button>
      </form>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
