<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) session_start();

/**
 * Cargar db.php buscando en 2 ubicaciones válidas:
 * 1) HOUSED/app/config/db.php
 * 2) HOUSED/config/db.php
 */
$candidates = [
  dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'db.php',
  dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'db.php',
];
$found = false;
foreach ($candidates as $p) {
  if (is_file($p)) { require_once $p; $found = true; break; }
}
if (!$found) {
  header('Content-Type: text/plain; charset=utf-8');
  die("No se encontró el archivo de conexión (db.php).\nIntenté:\n- " . implode("\n- ", $candidates) . "\n");
}

// Debe estar logueado
if (empty($_SESSION['user_id'])) {
  header('Location: index.php?page=miperfil');
  exit;
}

$userId = (int)$_SESSION['user_id'];
$errors = [];
$okMsg  = null;

// ===== Helpers =====
function get_user_by_id($id) {
  $db = db();
  $stmt = $db->prepare("SELECT id, email, nombre FROM users WHERE id=?");
  $stmt->bind_param('i', $id);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}

function email_enviar_cambio($oldEmail, $newEmail) {
  $mailConfigFile = dirname(__DIR__, 2) . '/config/mail.php';
  if (!is_file($mailConfigFile)) $mailConfigFile = dirname(__DIR__, 3) . '/config/mail.php';
  if (is_file($mailConfigFile)) require_once $mailConfigFile;

  $sentNew = false; $sentOld = false;

  $autoloads = [
    dirname(__DIR__, 3) . '/vendor/autoload.php',
    dirname(__DIR__, 2) . '/vendor/autoload.php',
    __DIR__ . '/../../vendor/autoload.php',
  ];
  $hasMailer = false;
  foreach ($autoloads as $a) { if (is_file($a)) { require_once $a; $hasMailer = true; break; } }

  if (defined('MAIL_ENABLED') && MAIL_ENABLED && $hasMailer) {
    try {
      $mail = new PHPMailer\PHPMailer\PHPMailer(true);
      if (defined('MAIL_SMTP') && MAIL_SMTP) {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = SMTP_PORT;
      }
      $mail->setFrom(FROM_EMAIL, FROM_NAME);
      $mail->addAddress($newEmail);
      $mail->Subject = 'HOUSED: cambio de correo';
      $mail->Body    = "Hola,\n\nTu correo fue actualizado correctamente a: $newEmail\n\nSaludos,\nHOUSED";
      $mail->send(); $sentNew = true;

      if ($oldEmail && $oldEmail !== $newEmail) {
        $mail2 = new PHPMailer\PHPMailer\PHPMailer(true);
        if (defined('MAIL_SMTP') && MAIL_SMTP) {
          $mail2->isSMTP();
          $mail2->Host       = SMTP_HOST;
          $mail2->SMTPAuth   = true;
          $mail2->Username   = SMTP_USER;
          $mail2->Password   = SMTP_PASS;
          $mail2->SMTPSecure = SMTP_SECURE;
          $mail2->Port       = SMTP_PORT;
        }
        $mail2->setFrom(FROM_EMAIL, FROM_NAME);
        $mail2->addAddress($oldEmail);
        $mail2->Subject = 'HOUSED: tu correo fue cambiado';
        $mail2->Body    = "Hola,\n\nSe cambió el correo de tu cuenta HOUSED a: $newEmail\nSi no fuiste tú, contáctanos.\n\nSaludos,\nHOUSED";
        $mail2->send(); $sentOld = true;
      }
    } catch (Throwable $e) { /* silencioso */ }
  } else {
    $headers = "From: HOUSED <no-reply@housed.local>\r\n";
    @mail($newEmail, "HOUSED: cambio de correo",
          "Hola,\n\nTu correo fue actualizado correctamente a: $newEmail\n\nSaludos,\nHOUSED", $headers);
    $sentNew = true;
    if ($oldEmail && $oldEmail !== $newEmail) {
      @mail($oldEmail, "HOUSED: tu correo fue cambiado",
            "Hola,\n\nSe cambió el correo de tu cuenta HOUSED a: $newEmail\nSi no fuiste tú, contáctanos.\n\nSaludos,\nHOUSED", $headers);
      $sentOld = true;
    }
  }
  return [$sentNew, $sentOld];
}

// ===== Acciones POST =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'logout') {
    session_unset();
    session_destroy();
    header('Location: index.php?page=home');
    exit;
  }

  if ($action === 'save') {
    $newEmail = trim($_POST['email'] ?? '');
    $newName  = trim($_POST['nombre'] ?? '');

    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) $errors[] = 'Correo inválido.';

    if (!$errors) {
      try {
        $db   = db();
        $user = get_user_by_id($userId);
        if (!$user) throw new Exception('Usuario no encontrado');

        $oldEmail = $user['email'];

        if (strcasecmp($newEmail, $oldEmail) !== 0) {
          $chk = $db->prepare("SELECT id FROM users WHERE email=? AND id<>?");
          $chk->bind_param('si', $newEmail, $userId);
          $chk->execute(); $chk->store_result();
          if ($chk->num_rows > 0) $errors[] = 'Ese correo ya está vinculado con otra cuenta.';
        }

        if (!$errors) {
          $upd = $db->prepare("UPDATE users SET email=?, nombre=? WHERE id=?");
          $upd->bind_param('ssi', $newEmail, $newName, $userId);
          $upd->execute();

          $_SESSION['email']  = $newEmail;
          $_SESSION['nombre'] = $newName;

          if (strcasecmp($newEmail, $oldEmail) !== 0) {
            [$sentNew, $sentOld] = email_enviar_cambio($oldEmail, $newEmail);
            $okMsg = 'Datos guardados. ' . (($sentNew || $sentOld) ? 'Se enviaron notificaciones de cambio de correo.' : 'No se pudo enviar notificación por correo (configurar SMTP).');
          } else {
            $okMsg = 'Datos guardados.';
          }
        }
      } catch (mysqli_sql_exception $e) {
        $errors[] = ($e->getCode() === 1062) ? 'Ese correo ya está vinculado con otra cuenta.' : 'Error del servidor: '.$e->getMessage();
      } catch (Throwable $e) { $errors[] = 'Error del servidor: '.$e->getMessage(); }
    }
  }
}

// Cargar datos actuales
$db   = db();
$user = get_user_by_id($userId);
$emailActual  = $user['email'] ?? ($_SESSION['email'] ?? '');
$nombreActual = $user['nombre'] ?? ($_SESSION['nombre'] ?? '');
$primeraVez   = empty($nombreActual);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mi Perfil - HOUSED</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="css/stylesComponentes.css">
  <link rel="stylesheet" href="css/miperfil.css">
  <link rel="stylesheet" href="css/perfil.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<?php include __DIR__ . '/../componentes/navbar.php'; ?>

<!-- Hero con animación -->
<section class="seccion-perfil">
  <div class="inner">
    <h1>Mi Perfil</h1>
    <p>Gestiona tu información de cuenta.</p>
  </div>
</section>

<!-- Contenido principal en tarjetas -->
<section class="contenedor">
  <div class="grid-perfil">

    <article class="tarjeta-perfil">
      <div class="tarjeta-header">
        <i class="fa-solid fa-user-pen"></i>
        <div>
          <h3>Datos de la cuenta</h3>
          <p>Completa tu nombre y actualiza tu correo.</p>
        </div>
      </div>

      <?php if (!empty($errors)): ?>
        <div class="alert error">
          <ul><?php foreach ($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>'; ?></ul>
        </div>
      <?php elseif (!empty($okMsg)): ?>
        <div class="alert success"><?= htmlspecialchars($okMsg) ?></div>
      <?php endif; ?>

      <form method="post" action="index.php?page=perfil" class="formulario-perfil" novalidate>
        <input type="hidden" name="action" value="save">

        <div class="grupo-input">
          <label for="email">Correo</label>
          <div class="input-con-icono">
            <i class="fa-solid fa-envelope"></i>
            <input type="email" id="email" name="email" required
                   value="<?= htmlspecialchars($emailActual) ?>">
          </div>
        </div>

        <div class="grupo-input">
          <label for="nombre">Nombre</label>
          <div class="input-con-icono">
            <i class="fa-solid fa-user"></i>
            <input type="text" id="nombre" name="nombre" placeholder="Tu nombre"
                   <?= $primeraVez ? 'autofocus' : '' ?>
                   value="<?= htmlspecialchars($nombreActual) ?>">
          </div>
        </div>

        <div class="acciones">
          <button type="submit" class="boton-login">
            Guardar cambios <i class="fa-solid fa-floppy-disk"></i>
          </button>
        </div>
      </form>
    </article>

    <article class="tarjeta-perfil secundaria">
      <div class="tarjeta-header">
        <i class="fa-solid fa-shield-halved"></i>
        <div>
          <h3>Sesión</h3>
          <p>Cierra tu sesión de forma segura.</p>
        </div>
      </div>

      <form method="post" action="index.php?page=perfil">
        <input type="hidden" name="action" value="logout">
        <button type="submit" class="boton-login btn-logout">
          Cerrar sesión <i class="fa-solid fa-right-from-bracket"></i>
        </button>
      </form>
    </article>

  </div>
</section>

<?php include __DIR__ . '/../componentes/footer.php'; ?>
</body>
</html>