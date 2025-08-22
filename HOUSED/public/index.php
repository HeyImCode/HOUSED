<?php
// Router simple de HOUSED (usa index.php?page=xxxx)

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

  case 'miperfil':  // Página de login/registro (si NO está logueado)
    include __DIR__ . '/../app/views/pages/miperfil.php';
    break;

  case 'perfil':    // NUEVA: Perfil del usuario (si SÍ está logueado)
    include __DIR__ . '/../app/views/pages/perfil.php';
    break;

  case 'comprar':
    include __DIR__ . '/../app/views/pages/comprar.php';
    break;

  case 'registro':
  case 'register':
    include __DIR__ . '/../app/views/pages/register.php';
    break;

  default:
    include __DIR__ . '/../app/views/pages/index.php';
    break;
}