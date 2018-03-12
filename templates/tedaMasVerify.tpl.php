<?php 
    
    if($cedula && $recurso){
        $get= new tedaMas();
        print $get->API($recurso,$cedula);
    }
    
?>