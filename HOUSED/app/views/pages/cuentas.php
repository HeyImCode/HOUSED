<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) session_start();

/* Solo Admin */
if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
  http_response_code(403);
  header('Content-Type: text/plain; charset=utf-8');
  echo "403 - Prohibido (solo administradores).";
  exit;
}

/* Cargar DB */
$candidates = [
  dirname(__DIR__, 2) . '/config/db.php',
  dirname(__DIR__, 3) . '/config/db.php',
];
$found = false;
foreach ($candidates as $p) { if (is_file($p)) { require_once $p; $found = true; break; } }
if (!$found) { header('Content-Type: text/plain; charset=utf-8'); die("Falta config/db.php"); }

/* CSRF */
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
$csrf = $_SESSION['csrf'];

$flash = null;

/* Acciones: toggle (activar/desactivar) y delete */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  $uid    = (int)($_POST['user_id'] ?? 0);
  $token  = $_POST['csrf'] ?? '';

  if (!$uid || $token !== $_SESSION['csrf']) {
    $flash = ['type' => 'error', 'msg' => 'Solicitud inválida.'];
  } else {
    try {
      $db = db();
      if ($action === 'toggle') {
        // No te desactives a ti mismo
        if ($uid === (int)$_SESSION['user_id']) {
          $flash = ['type' => 'error', 'msg' => 'No puedes desactivar tu propia cuenta.'];
        } else {
          $db->query("UPDATE users SET is_active = 1 - is_active WHERE id = ".(int)$uid);
          $flash = ['type' => 'success', 'msg' => 'Estado actualizado.'];
        }
      } elseif ($action === 'delete') {
        // No te borres a ti mismo
        if ($uid === (int)$_SESSION['user_id']) {
          $flash = ['type' => 'error', 'msg' => 'No puedes borrar tu propia cuenta.'];
        } else {
          $db->query("DELETE FROM users WHERE id = ".(int)$uid." LIMIT 1");
          $flash = ['type' => 'success', 'msg' => 'Cuenta eliminada.'];
        }
      }
    } catch (Throwable $e) {
      $flash = ['type' => 'error', 'msg' => 'Error: '.$e->getMessage()];
    }
  }
}

/* Obtener usuarios */
$rows = [];
try {
  $db = db();
  $q  = $db->query("SELECT id, email, nombre, role, is_active, created_at FROM users ORDER BY created_at DESC");
  while ($r = $q->fetch_assoc()) $rows[] = $r;
} catch (Throwable $e) {
  header('Content-Type: text/plain; charset=utf-8');
  die('Error DB: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <title>Cuentas | Admin - HOUSED</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="css/stylesComponentes.css?v=6">
  <link rel="stylesheet" href="css/miperfil.css">
  <link rel="stylesheet" href="css/perfil.css">
  <style>
    /* Tabla minimal, acorde al estilo */
    .tabla { width:100%; border-collapse: collapse; }
    .tabla th, .tabla td { padding:10px 12px; border-bottom:1px solid #eee; text-align:left; }
    .tabla th { background:#fafafa; font-weight:600; }
    .badge { display:inline-block; padding:2px 8px; border-radius:999px; font-size:.85rem }
    .ok { background:#e6f7ed; color:#107a3a; }
    .off { background:#fdecec; color:#991b1b; }
    .role { background:#eef2ff; color:#3730a3; }
    .acciones { display:flex; gap:8px; }
    .btn-sec { background:#fff; border:1px solid #ddd; border-radius:8px; padding:8px 12px; cursor:pointer }
    .btn-sec:hover { background:#f7f7f7 }
    .btn-del { background:#f43f5e; color:#fff; border-color:#f43f5e }
    .btn-del:hover { filter:brightness(.95) }
  </style>
</head>
<body>
<?php include __DIR__ . '/../componentes/navbar.php'; ?>

<section class="seccion-perfil">
  <div class="inner">
    <h1>Cuentas</h1>
    <p>Administración de usuarios</p>
  </div>
</section>

<section class="contenedor">
  <article class="tarjeta-perfil" style="overflow:auto">
    <?php if ($flash): ?>
      <div class="alert <?= $flash['type']==='success' ? 'success' : 'error' ?>">
        <?= htmlspecialchars($flash['msg']) ?>
      </div>
    <?php endif; ?>

    <table class="tabla">
      <thead>
        <tr>
          <th>ID</th>
          <th>Correo</th>
          <th>Nombre</th>
          <th>Rol</th>
          <th>Estado</th>
          <th>Creado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($rows as $u): ?>
        <tr>
          <td><?= (int)$u['id'] ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= htmlspecialchars($u['nombre'] ?? '') ?></td>
          <td><span class="badge role"><?= htmlspecialchars($u['role']) ?></span></td>
          <td>
            <?php if ((int)$u['is_active'] === 1): ?>
              <span class="badge ok">Activa</span>
            <?php else: ?>
              <span class="badge off">Inactiva</span>
            <?php endif; ?>
          </td>
          <td><small><?= htmlspecialchars($u['created_at']) ?></small></td>
          <td>
            <div class="acciones">
              <form method="post" action="index.php?page=cuentas" onsubmit="return confirm('¿Seguro que deseas cambiar el estado?');">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                <button class="btn-sec" type="submit">
                  <?= (int)$u['is_active'] === 1 ? 'Desactivar' : 'Activar' ?>
                </button>
              </form>

              <form method="post" action="index.php?page=cuentas" onsubmit="return confirm('¿Seguro que deseas BORRAR esta cuenta? Esta acción no se puede deshacer.');">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                <button class="btn-sec btn-del" type="submit">Borrar</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </article>
</section>

<?php include __DIR__ . '/../componentes/footer.php'; ?>
</body>
</html>