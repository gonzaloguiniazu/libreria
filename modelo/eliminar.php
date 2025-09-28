<?php


if (!empty($_GET["id"])) {
    $id = $_GET["id"];

    $eliminar = $conn->query("delete from producto where id_producto = $id");

    if ($eliminar == 1) {
        echo "<div class= 'alert alert-success'>Registro eliminado con exito</div>";
    } else {
        echo "<div class= 'alert alert-danger'>Error al eliminar</div>";
    }
?>
    <!-- script para que no te salga el cartel de reenviar formulario -->
    <script>
        history.replaceState(null, null, location.pathname);
    </script>

<?php
}
