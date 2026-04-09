<?php

include "../../config/konesi.php";

$s = "SELECT * FROM datasiswa";
$q = mysqli_query($konek, $s);

while($dta = mysqli_fetch_array($q)){
    $nokartu = $dta['nokartu'];
    $s_up = "UPDATE datasiswa SET nis = '$nokartu' WHERE nokartu = '$nokartu'";
    $up = mysqli_query($konek, $s_up);
    
    if($up){
        echo $nokartu . " OK <br>";
    } else {
        echo $nokartu . " ERROR <br>";
    }
}

mysqli_close($konek);