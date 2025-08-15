<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios - HOUSED</title>

    <link rel="stylesheet" href="css/stylesComponentes.css">
    <link rel="stylesheet" href="css/servicios.css">
</head>
<body>

<?php include __DIR__ . '/../componentes/navbar.php'; ?>

<section class="seccion-servicios">
    <h1>Servicios Disponibles</h1>
    <p>Encuentra el servicio que deseas usar</p>
</section>

<section class="lista-servicios">
    <div class="contenedor">
        <div class="cuadricula-servicios">

            <!-- Compra un hogar -->
            <article class="tarjeta-servicios">
                <div class="cabecera-tarjeta">
                    <span class="etiqueta etiqueta--comprar">COMPRAR</span>
                </div>
                <div class="contenido-tarjeta">
                    <h3>Compra un hogar</h3>
                    <p>Busca tu nuevo hogar</p>
                </div>
                <div class="acciones">
                    <!-- Opción A: enlace directo al mapa -->
                    <a href="index.php?page=comprar" class="boton boton-primario">Buscar</a>
                </div>
            </article>

            <!-- Vende tu casa -->
            <article class="tarjeta-servicios">
                <div class="cabecera-tarjeta">
                    <span class="etiqueta etiqueta--vender">VENDER</span>
                </div>
                <div class="contenido-tarjeta">
                    <h3>Vende tu casa</h3>
                    <p>Publica tu casa para vender</p>
                </div>
                <div class="acciones">
                    <a href="#" class="boton boton-primario">Vender</a>
                </div>
            </article>

            <!-- Renta una casa -->
            <article class="tarjeta-servicios">
                <div class="cabecera-tarjeta">
                    <span class="etiqueta etiqueta--rentar">RENTAR</span>
                </div>
                <div class="contenido-tarjeta">
                    <h3>Renta una casa</h3>
                    <p>Busca una casa para rentar</p>
                </div>
                <div class="acciones">
                    <a href="#" class="boton boton-primario">Rentar</a>
                </div>
            </article>

        </div>
    </div>
</section>

<?php include __DIR__ . '/../componentes/footer.php'; ?>
</body>
</html>