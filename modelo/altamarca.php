<?php
if (!empty($_POST["registrar"])) {
    if (!empty($_POST["marca"])) {
        $marca = $_POST["marca"];
        $sql = $conn->query("insert into marca(nombre_marca) values('$marca')");

        if ($sql == 1) {
            echo '<div class= "alert alert-success">Marca registrada correctamente</div>';
        } else {
            echo '<div class= "alert alert-danger">No se pudo registrar la marca</div>';
        }
    } else {
        echo '<div class= "alert alert-warning">Alguno de los campos esta vacio</div>';
    }
    ?>
    <!-- script para que no te salga el cartel de reenviar formulario -->
    <script>
        history.replaceState(null, null, location.pathname);
    </script>

<?php

}
