<div style="position: relative;" class="sf_admin_form_row sf_admin_text formato_seguimiento_ver">
    <div>
        <font class="f16b">Serie Documental: </font>
        <font class="f16n"><?php if(isset($valores['solicitud_expediente_serie_documental_nombre'])) echo html_entity_decode($valores['solicitud_expediente_serie_documental_nombre']); ?></font>
    </div>
    <div style="padding-left: 112px; padding-top: 5px;">
        <font class="f16n">
            <?php 
            if(isset($valores['solicitud_expediente_clasificador_detalles'])) {
                foreach ($valores['solicitud_expediente_clasificador_detalles'] as $nombre => $valor) {
                    echo '<u>'.$nombre.':</u> '.html_entity_decode($valor).'<br/>';
                } 
            }
            ?>
        </font>
    </div>
    <hr>
    <div>
        <font class="f16b">Motivo de la solicitud: </font>
        <font class="f16n"><?php if(isset($valores['solicitud_expediente_motivo'])) echo html_entity_decode($valores['solicitud_expediente_motivo']); ?></font>
    </div>
    <div>
        <font class="f16b">Tipo de prestamo: </font>
        <font class="f16n">
            <?php 
                if($valores['solicitud_expediente_prestamo_fisico']=='t' && $valores['solicitud_expediente_prestamo_digital']=='t')
                    echo "Fisico y Digital"; 
                else if ($valores['solicitud_expediente_prestamo_fisico']=='t')
                    echo "Fisico";
                else
                    echo "Digital";
            ?>
        </font>
    </div>
</div>
