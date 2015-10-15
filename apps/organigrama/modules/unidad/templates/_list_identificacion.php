<?php
  $cadena_espacios_count = count(explode('nbsp', $unidades_orden[$organigrama_unidad->getId()])) - 1 ;
  $cadena_epacios = ''; for($i=0;$i<$cadena_espacios_count;$i++) $cadena_epacios .= '&nbsp;';

  echo html_entity_decode($unidades_orden[$organigrama_unidad->getId()]);

  if($organigrama_unidad->getNombre() != $organigrama_unidad->getNombreReducido()){
    echo '<br/>'.html_entity_decode($cadena_epacios).'<font class="f13n gris_oscuro tooltip" title="Nombre reducido">'.$organigrama_unidad->getNombreReducido().'</font>'; 
  }
  
  echo '<br/>'.html_entity_decode($cadena_epacios).'<b class="tooltip" title="Siglas">'.$organigrama_unidad->getSiglas().'</b>'; 
?>