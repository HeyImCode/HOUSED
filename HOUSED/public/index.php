<?php
// Router simple de HOUSED
// Funciona con URLs como: index.php?page=servicios

// Obtener la página solicitada
$page = $_GET['page'] ?? 'home';

// Definir las páginas disponibles
switch ($page) {
    case 'home':
    case 'inicio':
        include '../app/views/pages/index.php';
        break;
        
    case 'miperfil':
    case 'perfil':
    case 'login':
        include '../app/views/pages/miperfil.php';
        break;
        
    case 'servicios':
        include '../app/views/pages/servicios.php';
        break;
        
    case 'faq':
    case 'preguntas':
        include '../app/views/pages/faq.php';
        break;
        
    default:
        // Si la página no existe, mostrar inicio
        include '../app/views/pages/index.php';
        break;
}
?>