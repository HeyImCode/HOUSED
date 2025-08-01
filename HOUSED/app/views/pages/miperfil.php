<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil - HOUSED</title>

    <!-- CSS base -->
<link rel="stylesheet" href="css/stylesComponentes.css">
<link rel="stylesheet" href="css/miperfil.css" id="css-miperfil">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

    <!-- Navbar -->
<?php include __DIR__ . '/../componentes/navbar.php'; ?>

    <div class="contenedor-login">
        <!-- Sección de bienvenida -->
        <div class="seccion-bienvenida">
            <div class="contenido-bienvenida">
                <h1>¡Bienvenido a HOUSED!</h1>
                <p>Donde puedes encontrar tu nuevo hogar ideal.</p>
                <div class="caracteristicas">
                    <div class="caracteristica">
                        <i class="fas fa-key"></i>
                        <span>Compra</span>
                    </div>
                    <div class="caracteristica">
                        <i class="fas fa-hand-holding-usd"></i>
                        <span>Vende</span>
                    </div>
                    <div class="caracteristica">
                        <i class="fas fa-file-contract"></i>
                        <span>Renta</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario de login -->
        <div class="seccion-formulario">
            <div class="contenedor-form">
                <div class="header-form">
                    <h2>Iniciar Sesión</h2>
                    <p>Accede a tu cuenta para continuar</p>
                </div>

                <form class="formulario-login" action="#" method="POST">
                    <div class="grupo-input">
                        <label for="email">Correo electrónico</label>
                        <div class="input-con-icono">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" placeholder="tucorreo@ejemplo.com" required>
                        </div>
                    </div>

                    <div class="grupo-input">
                        <label for="password">Contraseña</label>
                        <div class="input-con-icono">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" placeholder="••••••••" required>
                            <button type="button" class="toggle-password" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggle-icon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="boton-login">
                        <span>Iniciar Sesión</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>

                <div class="registro-link">
                    <p>¿No tienes una cuenta? <a href="#">Regístrate aquí</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
<?php include __DIR__ . '/../componentes/footer.php'; ?>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggle-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>

</body>
</html>