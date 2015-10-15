<?php

class wsInputCorrespondencia {
    
    public function generarArray($cadena) {
        $serializado= urldecode($cadena);
        $ready_ar= unserialize($serializado);
        
        //INSERTAR CORRESPONDENCIAS (RECEPCION AUTOMATICA DE CORRESPONDENCIA EXTERNA)
        
        return $ready_ar;
    }
}