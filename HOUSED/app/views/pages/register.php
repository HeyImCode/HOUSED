<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Carga db.php buscando en 2 ubicaciones válidas:
 * 1) HOUSED/app/config/db.php
 * 2) HOUSED/config/db.php
 */
$candidates = [
  dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'db.php', // app/config/db.php
  dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'db.php', // config/db.php
];

$found = false;
foreach ($candidates as $p) {
  if (is_file($p)) { require_once $p; $found = true; break; }
}
if (!$found) {
  header('Content-Type: text/plain; charset=utf-8');
  die("No se encontró el archivo de conexión (db.php).\nIntenté:\n- " . implode("\n- ", $candidates) . "\n");
}

$errors = [];

/**
 * Procesa el registro ANTES de enviar HTML (para poder redirigir con header()).
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $pass  = $_POST['password'] ?? '';
  $pass2 = $_POST['password2'] ?? '';

  // Validaciones básicas
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Correo electrónico inválido.';
  if (strlen($pass) < 8) $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
  if ($pass !== $pass2) $errors[] = 'Las contraseñas no coinciden.';

  if (!$errors) {
    try {
      $db = db();

      // Asegura la tabla (por si aún no corriste el SQL en phpMyAdmin)
      $db->query("
        CREATE TABLE IF NOT EXISTS users (
          id INT AUTO_INCREMENT PRIMARY KEY,
          email VARCHAR(120) NOT NULL UNIQUE,
          password_hash VARCHAR(255) NOT NULL,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
      ");

      // Verificación explícita de duplicado (mensaje claro)
      $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
      $stmt->bind_param('s', $email);
      $stmt->execute(); $stmt->store_result();
      if ($stmt->num_rows > 0) {
        $errors[] = 'Ese correo ya está vinculado con una cuenta. Intenta iniciar sesión o usa otro correo.';
      } else {
        // Insertar usuario nuevo
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $ins  = $db->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?)");
        $ins->bind_param('ss', $email, $hash);
        $ins->execute();

        // Registro correcto → redirige a la página principal (ajusta 'home'/'inicio' según tu router)
        header('Location: index.php?page=home');
        exit;
      }
    } catch (mysqli_sql_exception $e) {
      // Si la UNIQUE key de email dispara error 1062, mostramos el mismo mensaje
      if ($e->getCode() === 1062 || stripos($e->getMessage(), 'duplicate') !== false) {
        $errors[] = 'Ese correo ya está vinculado con una cuenta. Intenta iniciar sesión o usa otro correo.';
      } else {
        $errors[] = 'Error del servidor: ' . $e->getMessage();
      }
    } catch (Throwable $e) {
      $errors[] = 'Error del servidor: ' . $e->getMessage();
    }
  }
}

// A partir de aquí ya podemos imprimir HTML
require_once __DIR__ . '/../componentes/navbar.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Crear cuenta - HOUSED</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- Reusa los estilos del login para que se vea igual -->
  <link rel="stylesheet" href="css/stylesComponentes.css">
  <link rel="stylesheet" href="css/miperfil.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>

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

  <!-- Panel derecho (formulario de registro) -->
  <section class="seccion-formulario">
    <div class="contenedor-form">
      <div class="header-form">
        <h2>Crear cuenta</h2>
        <p>Ingresa tu correo y crea tu contraseña.</p>
      </div>

      <?php if (!empty($errors)): ?>
        <div class="alert error">
          <ul><?php foreach ($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>'; ?></ul>
        </div>
      <?php endif; ?>

      <form method="post" action="index.php?page=register" class="formulario-login" novalidate>
        <div class="grupo-input">
          <label for="email">Correo electrónico</label>
          <div class="input-con-icono">
            <i class="fa-solid fa-envelope"></i>
            <input type="email" id="email" name="email" placeholder="tucorreo@ejemplo.com" required
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          </div>
        </div>

        <div class="grupo-input">
          <label for="password">Contraseña</label>
          <div class="input-con-icono">
            <i class="fa-solid fa-lock"></i>
            <input type="password" id="password" name="password" minlength="8" placeholder="Mínimo 8 caracteres" required>
          </div>
        </div>

        <div class="grupo-input">
          <label for="password2">Repetir contraseña</label>
          <div class="input-con-icono">
            <i class="fa-solid fa-lock"></i>
            <input type="password" id="password2" name="password2" minlength="8" placeholder="Repite tu contraseña" required>
          </div>
        </div>

        <button type="submit" class="boton-login">
          Crear cuenta <i class="fa-solid fa-arrow-right-long"></i>
        </button>

        <p class="texto-registro" style="text-align:center; margin-top:.75rem;">
          ¿Ya tienes cuenta? <a href="index.php?page=miperfil">Inicia sesión</a>
        </p>
      </form>
    </div>
  </section>
</div>

<?php include __DIR__ . '/../componentes/footer.php'; ?>
</body>
</html>