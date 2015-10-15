<div>
    <a href="#" onclick="reiniciar_fondo(); return false;">
        <img src="/images/icon/color_fill.png"/>&nbsp;Seleccionar otro fondo
    </a>
</div>
<hr/>

<div id="div_carnet_fondo" style="position: relative; width: 0px; min-height: 325px;">
    <div id="contenedor" style="position: absolute; top: 0px; left: 0px; width: 204px; height: 325px;">
        <img src="/images/carnet/<?php echo $nombre_final_fondo; ?>" style="border: 1px solid; width: 204px; height: 325px"/>
        <input name="seguridad_carnet_diseno[imagen_fondo]" type="hidden" value="<?php echo $nombre_final_fondo; ?>"/>
    </div>
    <div id="div_parametros_diseno" style="position: absolute; top: 0px; left: 0px;">&nbsp;</div>
</div>

<script> preparar_parametros(); </script>