<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>serivcios - HOUSED</title>
    
    <!-- CSS base (siempre se carga) -->
<link rel="stylesheet" href="css/stylesComponentes.css">
    
    <!-- CSS específico para serivcios -->
<link rel="stylesheet" href="css/servicios.css">
</head>
<body>
    
    <!-- Aqui agregamos el componente del navbar -->
<?php include __DIR__ . '/../componentes/navbar.php'; ?>

    <!-- Sección principal -->
    <section class="seccion-servicios">
        <div class="contenedor">
            <h1>Servicios Disponibles</h1>
            <p>Encuentra el servicio que deseas usar </p>
    </section>

    <!-- Lista de Servicios -->
    <section class="lista-servicios">
        <div class="contenedor">
            <div class="cuadricula-servicios">



                <!-- Servicio 1 -->
                <div class="tarjeta-servicios">
                    <div class="categoria-tag">Comprar</div>
                    <h3>Compra un hogar</h3>
                    <p>Busca tu nuevo hogar!</p>

                    <div class="acciones">
                        <button class="boton boton-primario">Buscar</button>
                    </div>
                    
               </div>



                 <!-- Servicio 2 -->
                <div class="tarjeta-servicios">
                    <div class="categoria-tag">Vender</div>
                    <h3>Vende tu casa</h3>
                    <p>Publica tu casa pra vender!</p>

                    <div class="acciones">
                        <button class="boton boton-primario">Vender</button>
                    </div>
                </div>


                <!-- Servicio 3 -->
                <div class="tarjeta-servicios">
                    <div class="categoria-tag">Rentar</div>
                    <h3>Renta una casa</h3>
                    <p class="descripcion">Busca una casa para rentar!</p>

                    <div class="acciones">
                        <button class="boton boton-primario">Rentar</button>
                    </div>
 
                </div>

            </div>
        </div>
    </section>
    
    <!-- Aqui agregamos el componente del footer -->
<?php include __DIR__ . '/../componentes/footer.php'; ?>
</body>
</html>