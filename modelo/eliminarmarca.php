<?php
if (!empty($_GET["id"])) {
    $id = $_GET["id"];
    $sql = $conn->query("delete from marca where id_marca=$id");
    if ($sql == 1) {
        echo '<div class="alert alert-success">Marca eliminada correctamente</div>';
    } else {
        echo '<div class="alert alert-danger">Error al eliminar</div>';
    }
}
