<?php
if(!empty($_POST["registrar"])){
    if(!empty($_POST["marca"])){
        $id=$_POST["id"];
        $marca=$_POST["marca"];
        $sql=$conn->query("update marca set nombre_marca='$marca' where id_marca=$id");
        if($sql==1){
            header("location:altamarca_index.php");
        }
        else{
            echo "<div class= 'alert alert-danger'>Error al modificar marca</div>";
        }
        

    }else{
        echo "<div class= 'alert alert-warning'>campo vacio</div>";
    }

}



?>