<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<nav role="navigation">
  <div class="contenedor-navegacion">
    <a href="index.php" class="logo">HOUSED</a>
    <ul class="menu-navegacion">
      <li><a href="index.php?page=servicios">Servicios</a></li>
      <li><a href="index.php?page=faq">FAQ</a></li>
      <?php if (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
        <li><a href="index.php?page=cuentas">Cuentas</a></li>
      <?php endif; ?>
      <li><a href="index.php?page=miperfil">Mi Perfil</a></li>
    </ul>
  </div>
</nav>