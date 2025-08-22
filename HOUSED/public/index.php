<?php
// Router simple de HOUSED (usa index.php?page=xxxx)
if (session_status() === PHP_SESSION_NONE) session_start();

$page = $_GET['page'] ?? 'home';

switch ($page) {
  case 'home':
  case 'inicio':
    include __DIR__ . '/../app/views/pages/index.php';
    break;

  case 'servicios':
    include __DIR__ . '/../app/views/pages/servicios.php';
    break;

  case 'faq':
  case 'preguntas':
    include __DIR__ . '/../app/views/pages/faq.php';
    break;

  case 'miperfil':
  case 'login':
    include __DIR__ . '/../app/views/pages/miperfil.php';
    break;

  case 'perfil':
    include __DIR__ . '/../app/views/pages/perfil.php';
    break;

  case 'comprar':
    include __DIR__ . '/../app/views/pages/comprar.php';
    break;

  case 'register':
  case 'registro':
    include __DIR__ . '/../app/views/pages/register.php';
    break;

  /* === NUEVO: página exclusiva de Admin === */
  case 'cuentas':
    include __DIR__ . '/../app/views/pages/cuentas.php';
    break;

  default:
    include __DIR__ . '/../app/views/pages/index.php';
    break;
}