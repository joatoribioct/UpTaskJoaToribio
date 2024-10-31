<div class="contenedor restablecer">
    <?php include_once __DIR__ .'/../templates/nombrecitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Coloca tu nuevo Password</p>
        <?php include_once __DIR__ .'/../templates/alertas.php'; ?>

        <?php if($mostrar === true) { ?>
        <form class="formulario" method="POST">

            <div class="campo">
                <label for="password">Password</label>
                <input 
                    type="password"
                    id="password"
                    placeholder="Tu Password"
                    name="password"
                />
            </div>

            <input type="submit" class="boton" value="Guardar Password">
        </form>
        <?php } ?>

        <div class="acciones">
            <a href="/crear">¿Aún no tienes una Cuenta? crea una aqui</a>
            <a href="/olvide">¿Olvidaste tu Password? click aqui para recuperar</a>
        </div>
    </div>
</div>