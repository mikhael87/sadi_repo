<?php
use_helper('jQuery');

$clasificadores = Doctrine::getTable('Archivo_Clasificador')->detallesClasificadores($archivo_serie_documental->getId());

foreach ($clasificadores as $clasificador) {
    echo "<div class='tooltip' title='[!]Tipo de dato:[/!] ".$clasificador->getTipoDato()."<br/>[!]Parametros:[/!] ".$clasificador->getParametros()."'>".$clasificador->getNombre()."<div/>";    
} ?>