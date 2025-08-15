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

  case 'miperfil':
  case 'perfil':
    include __DIR__ . '/../app/views/pages/miperfil.php';
    break;

  case 'comprar':
    // NUEVA página del mapa tipo Zillow
    include __DIR__ . '/../app/views/pages/comprar.php';
    break;

  default:
    // Si la página no existe, vuelve a inicio
    include __DIR__ . '/../app/views/pages/index.php';
    break;
}