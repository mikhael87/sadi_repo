<?php use_helper('jQuery'); ?>

<tr class="sf_admin_form_row sf_admin_text sf_admin_filter_field">
    <td>
        <label for="archivo_expediente_filters_serie_documental">Serie Documental</label>
    </td>
    <td>
    <script>
        function fn_actualizar_detalles()
        {
            var serie = $("#serie_filter").val();

            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_archivo_url'); ?>expediente/listarClasificadoresFilter',
                type:'POST',
                dataType:'html',
                data:'s_id='+serie,
                beforeSend: function(Obj){
                              $('#icono_carga').show();
                        },
                success:function(data, textStatus){
                    $('#div_clasificadores').html('');
                    jQuery('#div_clasificadores').append(data);
                    $('#icono_carga').hide();
            }});

            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_archivo_url'); ?>expediente/listarTipologiasFilter',
                type:'POST',
                dataType:'html',
                data:'s_id='+serie,
                beforeSend: function(Obj){
                              $('#icono_carga').show();
                        },
                success:function(data, textStatus){
                    $('#div_tipologia').html('');
                    jQuery('#div_tipologia').append(data);
                    $('#icono_carga').hide();
            }});

            $('#div_etiquetas').html('Seleccione la tipologia documental');
        }
    </script>
        <select id="serie_filter" name="archivo_expediente_filters[serie_documental_id]" onchange="javascript: fn_actualizar_detalles();">
            <option value=""></option>
            <?php
            $series = Doctrine::getTable('Archivo_Compartir')->seriesCompartidas();

            foreach ($series as $serie) {
                ?>
                <option value="<?php echo $serie->getId(); ?>"><?php echo $serie->getNombre(); ?></option>
            <?php } ?>
        </select>
        &nbsp;<?php echo image_tag('icon/cargando.gif', array('id' => 'icono_carga', 'style' => 'vertical-align: middle; display: none')); ?>
        <br>
    </td>
</tr>

<tr>
    <td><label for="archivo_expediente_filters_clasificadores">Clasificadores</label></td>
    <td>
        <div id="div_clasificadores">Seleccione la serie documental</div>
    </td>
</tr>

<tr>
    <td><label for="archivo_expediente_filters_tipologia">Tipologia Documental</label></td>
    <td>
        <div id="div_tipologia">Seleccione la serie documental</div>
    </td>
</tr>

<tr>
    <td><label for="archivo_expediente_filters_etiquetas">Etiquetas</label></td>
    <td>
        <div id="div_etiquetas">Seleccione la tipologia documental</div>
    </td>
</tr>