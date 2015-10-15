<?php
$valores_etiquetas = Doctrine::getTable('Archivo_DocumentoEtiqueta')->valoresEtiquetas($archivo_documento->getId());

foreach ($valores_etiquetas as $valores) {
    echo $valores->getEtiqueta().': '.$valores->getValor()."<br/>";    
} ?>
