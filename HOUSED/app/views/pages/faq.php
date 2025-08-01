<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>FAQ - HOUSED</title>

    <!-- CSS base -->
<link rel="stylesheet" href="css/stylesComponentes.css">

<link rel="stylesheet" href="css/faq.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

    <!-- Navbar -->
<?php include __DIR__ . '/../componentes/navbar.php'; ?>

    <!-- Sección principal -->
    <section class="seccion-faq">
        <div class="contenedor">
            <h1>Preguntas Frecuentes</h1>
            <p>Encuentra respuestas a las dudas más comunes sobre HOUSED</p>
        </div>
    </section>

    <!-- Lista de preguntas -->
    <section class="lista-faq">
        <div class="contenedor">
            <div class="cuadricula-faq">

                <!-- Pregunta 1 -->
                <div class="tarjeta-faq">
                    <div class="categoria-tag">General</div>
                    <div class="pregunta-header" onclick="toggleFAQ(this)">
                        <h3>¿Qué es HOUSED?</h3>
                        <i class="fas fa-chevron-down icono-toggle"></i>
                    </div>
                    <div class="respuesta">
                        <p>HOUSED es una plataforma web de bienes raíces diseñada para ayudarte a encontrar, vender o rentar propiedades de forma rápida, segura y sencilla.</p>
                    </div>
                </div>

                <!-- Pregunta 2 -->
                <div class="tarjeta-faq">
                    <div class="categoria-tag">Pago</div>
                    <div class="pregunta-header" onclick="toggleFAQ(this)">
                        <h3>¿Donde puedo descargar mis facturas?</h3>
                        <i class="fas fa-chevron-down icono-toggle"></i>
                    </div>
                    <div class="respuesta">
                        <p>En HOUSED, no se generan facturas directamente desde nuestra plataforma. Esto se debe a que HOUSED no participa en las transacciones de compra, venta o renta de forma directa.</p>
                        <p>Somos una plataforma de contacto entre personas interesadas en comprar, vender o alquilar propiedades. Las transacciones reales (pagos, contratos, facturas, etc.) se realizan fuera del sitio, directamente entre el comprador y el vendedor o arrendador.</p>
                    </div>
                </div>

                <!-- Pregunta 3 -->
                <div class="tarjeta-faq">
                    <div class="categoria-tag">Pago</div>
                    <div class="pregunta-header" onclick="toggleFAQ(this)">
                        <h3>¿Como se hacen los pagos?</h3>
                        <i class="fas fa-chevron-down icono-toggle"></i>
                    </div>
                    <div class="respuesta">
                        <p>En HOUSED, los pagos no se procesan directamente desde la plataforma. Esto es porque HOUSED funciona como un punto de encuentro entre personas interesadas en comprar, vender o rentar propiedades, pero no interviene directamente en las transacciones.</p>
                    </div>
                </div>

                <!-- Pregunta 4 -->
                <div class="tarjeta-faq">
                    <div class="categoria-tag">Venta</div>
                    <div class="pregunta-header" onclick="toggleFAQ(this)">
                        <h3>¿Qué requisitos necesito para publicar una casa?</h3>
                        <i class="fas fa-chevron-down icono-toggle"></i>
                    </div>
                    <div class="respuesta">
                        <p>Solo necesitas una cuenta en HOUSED y proporcionar los datos básicos de la propiedad: ubicación, precio, tipo, descripción y fotografías (opcional).</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Footer -->
<?php include __DIR__ . '/../componentes/footer.php'; ?>

    <script>
        function toggleFAQ(element) {
            const tarjeta = element.parentElement;
            const respuesta = tarjeta.querySelector('.respuesta');
            const icono = element.querySelector('.icono-toggle');
            
            // Toggle active class
            tarjeta.classList.toggle('activa');
            
            // Rotate icon
            icono.style.transform = tarjeta.classList.contains('activa') ? 'rotate(180deg)' : 'rotate(0deg)';
            
            // Close other FAQs
            const todasLasTarjetas = document.querySelectorAll('.tarjeta-faq');
            todasLasTarjetas.forEach(otraTarjeta => {
                if (otraTarjeta !== tarjeta && otraTarjeta.classList.contains('activa')) {
                    otraTarjeta.classList.remove('activa');
                    const otroIcono = otraTarjeta.querySelector('.icono-toggle');
                    otroIcono.style.transform = 'rotate(0deg)';
                }
            });
        }
    </script>

</body>
</html>