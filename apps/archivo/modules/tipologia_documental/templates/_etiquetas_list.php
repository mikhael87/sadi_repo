<?php
use_helper('jQuery');

$etiquetas = Doctrine::getTable('Archivo_Etiqueta')->detallesEtiquetaciones($archivo_tipologia_documental->getId());

foreach ($etiquetas as $etiqueta) {
    echo "<div class='tooltip' title='[!]Tipo de dato:[/!] ".$etiqueta->getTipoDato()."<br/>[!]Parametros:[/!] ".$etiqueta->getParametros()."'>".$etiqueta->getNombre()."<div/>";    
} ?>
