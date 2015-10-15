<?php use_helper('jQuery'); ?>
<?php include(sfConfig::get("sf_root_dir").'/apps/correspondencia/modules/formatos/lib/solicitudExpediente/assets.php'); ?>

<script>
    $(document).ready(function() {
        var id_serie_doc = '<?php echo (isset($formulario['solicitud_expediente_serie_documental']) ? $formulario['solicitud_expediente_serie_documental'] : 'new'); ?>';
        if(id_serie_doc != 'new') {
            fn_mostrar_descriptores(id_serie_doc);
        }
    });
    
    function fn_mostrar_descriptores(serie){
        serie = serie || $('#select_serie').val();
        var forma = $('#formato_tipo_formato_id').val();
        var id_correspondencia= '<?php echo (isset($formulario['id']) ? $formulario['id'] : ''); ?>';
        <?php
        echo jq_remote_function(array('update' => 'div_descriptores_serie',
        'url' => 'formatos/librerias',
        'with'=> "'forma='+forma+'&func=DescriptoresSerie&var[serie_id]='+serie+'&var[corres_id]='+id_correspondencia"));
        ?>
    };
</script>


<fieldset id="sf_fieldset_contenido">
<h2>Contenido</h2>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_memorandum_asunto">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'nombre_etiqueta')) ?>

    <div>
        <label for="nombre_etiqueta">Serie Documental</label>
        <div class="content">
            <?php
                $serie_documental_id= 'new';
                if(isset($formulario['solicitud_expediente_serie_documental'])) {
                    $serie_documental_id= $formulario['solicitud_expediente_serie_documental'];
                }
                
                $series_documentales = Doctrine_Query::create()
                                        ->select('sd.*, u.nombre as unidad')
                                        ->from('Archivo_SerieDocumental sd')
                                        ->innerJoin('sd.Organigrama_Unidad u')
                                        ->orderBy('sd.nombre')
                                        ->execute();
                
                $cadena = '<select name="correspondencia[formato][solicitud_expediente_serie_documental]" id="select_serie" onchange="javascript: fn_mostrar_descriptores(); return false;">';
                
                $cadena .= '<option value="0"></option>';
                foreach ($series_documentales as $serie_documental) {
                    $cadena .= '<option '. (($serie_documental->getId()== $serie_documental_id) ? 'selected' : '') .' value="'.$serie_documental->getId().'">'.$serie_documental->getNombre().' ('.$serie_documental->getUnidad().')'.'</option>';
                }
                
                $cadena .= '</select>';
                $cadena .= '<input type="hidden" id="val_serie_documental" name="val_serie_documental" value="" />';
                echo $cadena;
            ?>

        </div>
    </div>

    <div class="help">Seleccione la serie documental (tipo de expediente) que desea.</div>

</div>

<div id="div_descriptores_serie"></div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_memorandum_adjunto">
    <div>
        <label for="memorandum_adjunto">N° de Expediente</label>
        <div class="content">
            <input type="text" name="correspondencia[formato][solicitud_expediente_numero]" id="solicitud_expediente_numero" value="<?php if(isset($formulario['solicitud_expediente_numero'])) echo $formulario['solicitud_expediente_numero']; ?>" />
        </div>
    </div>
</div>
<input type="hidden" id="div_descriptores_serie_expediente" name="div_descriptores_serie_expediente" value="" />

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_memorandum_adjunto">
    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'cedula')) ?>
    
    <div>
        <label for="memorandum_adjunto">Motivo de la solicitud</label>
        <div class="content" style="width: 650px;">
            <textarea rows="4" cols="30" name="correspondencia[formato][solicitud_expediente_motivo]" id="solicitud_expediente_motivo"><?php if(isset($formulario['solicitud_expediente_motivo'])) echo $formulario['solicitud_expediente_motivo']; ?></textarea>
        </div>
    </div>

    <div class="help"></div>
</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_memorandum_adjunto">
    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'cedula')) ?>

    <div>
        <label for="memorandum_adjunto">Tipo de prestamo</label>
        <div class="content" style="width: 650px;">
            <input type="checkbox" class="val_tipos_prestamos" name="correspondencia[formato][solicitud_expediente_prestamo_fisico]" value="f" <?php echo ($formulario['solicitud_expediente_prestamo_fisico']== 't') ? 'checked' : '' ?>/>Fisico&nbsp;
            <input type="checkbox" class="val_tipos_prestamos" name="correspondencia[formato][solicitud_expediente_prestamo_digital]" value="d" <?php echo ($formulario['solicitud_expediente_prestamo_digital']== 't') ? 'checked' : '' ?>/>Digital
            <input type="hidden" id="val_tipo_prestamo_expediente" name="val_tipo_prestamo_expediente" value="" />
        </div>
    </div>

    <div class="help"></div>
</div>

</fieldset>