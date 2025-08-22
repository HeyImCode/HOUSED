<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) session_start();

$candidates = [
  dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'db.php', // app/config/db.php
  dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'db.php', // config/db.php
];
foreach ($candidates as $p) { if (is_file($p)) { require_once $p; break; } }

$errors = [];

/* Si YA está logueado → directo a perfil */
if (!empty($_SESSION['user_id'])) {
  header('Location: index.php?page=perfil');
  exit;
}

/* Procesar LOGIN */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? 'login') === 'login') {
  $email = trim($_POST['email'] ?? '');
  $pass  = $_POST['password'] ?? '';

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Correo electrónico inválido.';
  }
  if ($pass === '') {
    $errors[] = 'Contraseña requerida.';
  }

  if (!$errors) {
    try {
      if (!function_exists('db')) { throw new Exception('No se encontró db.php'); }
      $db = db();

      $stmt = $db->prepare("SELECT id, email, nombre, password_hash, role, is_active FROM users WHERE email = ? LIMIT 1");
      $stmt->bind_param('s', $email);
      $stmt->execute();
      $u = $stmt->get_result()->fetch_assoc();

      if ($u && (int)$u['is_active'] === 1 && password_verify($pass, $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id']   = (int)$u['id'];
        $_SESSION['email']     = $u['email'];
        $_SESSION['nombre']    = $u['nombre'] ?? '';
        $_SESSION['user_role'] = $u['role'] ?? 'user';

        header('Location: index.php?page=perfil');
        exit;
      } else {
        if ($u && (int)$u['is_active'] === 0) {
          $errors[] = 'Tu cuenta está desactivada. Contacta al administrador: admin@housed.com.';
        } else {
          $errors[] = 'Correo o contraseña incorrectos.';
        }
      }
    } catch (Throwable $e) {
      $errors[] = 'Error del servidor: ' . $e->getMessage();
    }
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Mi Perfil - HOUSED</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- CSS (rutas relativas a /public/) -->
  <link rel="stylesheet" href="css/stylesComponentes.css?v=6">
  <link rel="stylesheet" href="css/miperfil.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<?php include __DIR__ . '/../componentes/navbar.php'; ?>

<!-- CONTENEDOR PRINCIPAL -->
<div class="contenedor-login">
  <!-- Panel izquierdo -->
  <section class="seccion-bienvenida">
    <div class="contenido-bienvenida">
      <h1>¡Bienvenido a <strong>HOUSED</strong>!</h1>
      <p>Donde puedes encontrar tu nuevo hogar ideal.</p>
      <ul class="features">
        <li><i class="fa-solid fa-key"></i> Compra</li>
        <li><i class="fa-solid fa-tag"></i> Vende</li>
        <li><i class="fa-solid fa-house"></i> Renta</li>
      </ul>
    </div>
  </section>

  <!-- Panel derecho (formulario de login) -->
  <section class="seccion-formulario">
    <div class="contenedor-form">
      <div class="header-form">
        <h2>Inicia sesión</h2>
        <p>Ingresa tu correo y contraseña para continuar.</p>
      </div>

      <?php if (!empty($errors)): ?>
        <div class="alert error">
          <ul><?php foreach ($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>'; ?></ul>
        </div>
      <?php endif; ?>

      <form method="post" action="index.php?page=miperfil" class="formulario-login" novalidate>
        <input type="hidden" name="action" value="login">

        <!-- Email -->
        <div class="grupo-input">
          <label for="email">Correo electrónico</label>
          <div class="input-con-icono">
            <i class="fa-solid fa-envelope"></i>
            <input type="email" id="email" name="email" placeholder="tucorreo@ejemplo.com" required
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          </div>
        </div>

        <!-- Password -->
        <div class="grupo-input">
          <label for="password">Contraseña</label>
          <div class="input-con-icono">
            <i class="fa-solid fa-lock" id="toggle-icon" style="cursor:pointer"></i>
            <input type="password" id="password" name="password" placeholder="Tu contraseña" minlength="8" required>
            <button type="button" class="toggle-password" aria-label="Mostrar u ocultar contraseña">
              <i class="fa-regular fa-eye"></i>
            </button>
          </div>
        </div>

        <!-- Botón de login -->
        <button type="submit" class="boton-login">
          Iniciar sesión <i class="fa-solid fa-arrow-right-long"></i>
        </button>
      </form>

      <!-- Enlace a REGISTRO -->
      <p class="texto-registro" style="text-align:center; margin-top:.75rem;">
        ¿No tienes una cuenta?
        <a href="index.php?page=register" class="link-register">Regístrate aquí</a>
      </p>
    </div>
  </section>
</div>

<?php include __DIR__ . '/../componentes/footer.php'; ?>

<!-- JS: alternar visibilidad de contraseña -->
<script>
  const toggleBtn = document.querySelector('.toggle-password');
  if (toggleBtn) {
    toggleBtn.addEventListener('click', () => {
      const input = document.getElementById('password');
      const icon  = toggleBtn.querySelector('i');
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    });
  }
</script>

</body>
</html>