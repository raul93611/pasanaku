<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pasanaku — Iniciar sesión</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>assets/css/app.css" rel="stylesheet">
</head>
<body>

<div class="login-bg">
  <div class="login-card screen-enter">
    <div class="login-logo">
      <div class="brand-icon"><i class="bi bi-coin"></i></div>
      <span class="brand-name">Pasanaku</span>
    </div>
    <p class="login-subtitle">Gestión de ahorros rotativos</p>

    <?php if (!empty($error)): ?>
    <div class="flash-msg error mb-3">
      <i class="bi bi-exclamation-circle-fill"></i>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="?page=login&action=post">
      <div class="mb-3">
        <label class="form-label-sm">Correo electrónico</label>
        <input class="form-control-pk" type="email" name="email"
          value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
          placeholder="admin@pasanaku.com" required autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label-sm">Contraseña</label>
        <input class="form-control-pk" type="password" name="password"
          placeholder="••••••••" required>
      </div>
      <button class="btn-pk-primary w-100 justify-content-center py-2" type="submit">
        Ingresar <i class="bi bi-arrow-right"></i>
      </button>
    </form>

    <div class="text-center mt-3" style="font-size:12px;color:var(--pk-muted)">
      Acceso restringido al administrador
    </div>
  </div>
</div>

</body>
</html>
