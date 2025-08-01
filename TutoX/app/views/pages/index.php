<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOUSED - Tu lugar para encontrar un hogar</title>
    
    <!-- CSS base (siempre se carga) -->
<link rel="stylesheet" href="css/stylesComponentes.css">

    <!-- CSS específico para index -->
<link rel="stylesheet" href="css/index.css">
     
</head>
<body>
    
<?php include __DIR__ . '/../componentes/navbar.php'; ?>

    <!-- Sección principal -->
    <section class="seccion-principal">
        <div class="contenido-principal">
            <h1>Bienvenido a HOUSED</h1>
            <p>La plataforma en la que puedes encontrar tu nuevo hogar y más!</p>
            <div class="botones-accion">
                <a href="#" class="boton boton-primario">Buscar Ayuda</a>
                <a href="#" class="boton boton-secundario">Servicios</a>
            </div>
        </div>
    </section>

    <!-- Sección de características -->
    <section class="caracteristicas">
        <div class="contenedor">
            <h2>¿Qué puedes hacer en HOUSED?</h2>
            <div class="cuadricula-caracteristicas">
                <div class="tarjeta-caracteristica">
                    <div class="icono-caracteristica">🔑🏠</div>
                    <h3>Comprar un hogar</h3>
                    <p>Encuentra tu nuevo hogar.</p>
                </div>
                <div class="tarjeta-caracteristica">
                    <div class="icono-caracteristica">💰🏠</div>
                    <h3>Vende una casa</h3>
                    <p>Pon a la venta tu casa de forma fácil!</p>
                </div>
                <div class="tarjeta-caracteristica">
                    <div class="icono-caracteristica">🏠🔄</div>
                    <h3>Renta un lugar</h3>
                    <p>Ve opciones para rentar una casa.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Cómo funciona -->
    <section class="como-funciona">
        <div class="contenedor">
            <h2>¿Cómo funciona HOUSED?</h2>
            <div class="pasos">
                <div class="paso">
                    <div class="numero-paso">1</div>
                    <h3>Regístrate</h3>
                    <p>Crea tu perfil de HOUSED</p>
                </div>
                <div class="paso">
                    <div class="numero-paso">2</div>
                    <h3>Busca o Publica</h3>
                    <p>Busca el servicio que necesitas o publica el servicio que puedes ofrecer.</p>
                </div>
                <div class="paso">
                    <div class="numero-paso">3</div>
                    <h3>Busca o Recibe</h3>
                    <p>Encuentra casas que puedas comprar/rentar o recibe ofertas por la tuya.</p>
                </div>
                <div class="paso">
                    <div class="numero-paso">4</div>
                    <h3>Conecta</h3>
                    <p>Conéctate con otras personas y coordina tratos o haz negocios con ellos.</p>
                </div>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/../componentes/footer.php'; ?>
</body>
</html>