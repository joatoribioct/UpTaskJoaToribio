<div class="contenedor olvide">
    <?php include_once __DIR__ .'/../templates/nombrecitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Recuperar tu Password</p>

        <?php include_once __DIR__ .'/../templates/alertas.php'; ?>

        <?php if ($mostrar2 === true) {?>
        <form class="formulario" method="POST" action="/olvide" novalidate>
            <div class="campo">
                <label for="email">Email</label>
                <input 
                    type="email"
                    id="email"
                    placeholder="Tu Email"
                    name="email"
                />
            </div>

            <input type="submit" class="boton" value="Enviar Enlace de Recuperacion" href="/restablecer">
        </form>
        <?php } ?>
        <div class="acciones">
            <a href="/">¿Ya tienes Cuenta? Iniciar Sesion</a>
            <a href="/crear">¿Aún no tienes una Cuenta? crea una aqui</a>
        </div>
    </div>
</div>