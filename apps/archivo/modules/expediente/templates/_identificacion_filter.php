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

            if(serie!=''){
                $("#div_cuerpos").html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando cuerpos de la Serie...');
                $("#div_clasificadores").html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando clasificadores de la Serie...');
                $("#div_tipologia").html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando tipologias de la Serie...');

                $.ajax({
                    url:'<?php echo sfConfig::get('sf_app_archivo_url'); ?>expediente/listarCuerposFilter',
                    type:'POST',
                    dataType:'html',
                    data:'s_id='+serie,
                    success:function(data, textStatus){
                        $('#div_cuerpos').html(data);
                }});

                $.ajax({
                    url:'<?php echo sfConfig::get('sf_app_archivo_url'); ?>expediente/listarClasificadoresFilter',
                    type:'POST',
                    dataType:'html',
                    data:'s_id='+serie,
                    success:function(data, textStatus){
                        $('#div_clasificadores').html(data);
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
                        $('#div_tipologia').html(data);
                }});

                $('#div_etiquetas').html('Seleccione la tipologia documental');
            } else {
                $("#div_cuerpos").html("Seleccione la serie documental");
                $("#div_clasificadores").html("Seleccione la serie documental");
                $("#div_tipologia").html("Seleccione la serie documental");
            }
        }
    </script>
        <select id="serie_filter" name="archivo_expediente_filters[serie_documental_id]" onchange="javascript: fn_actualizar_detalles();">
            <option value=""></option>
            <?php
            $series = Doctrine::getTable('Archivo_Expediente')->seriesPropias();

            foreach ($series as $serie) {
                ?>
                <option value="<?php echo $serie->getId(); ?>"><?php echo $serie->getNombre(); ?></option>
            <?php } ?>
        </select>
        <br>
    </td>
</tr>

<tr>
    <td><label for="archivo_expediente_filters_cuerpos">Cuerpos</label></td>
    <td>
        <div id="div_cuerpos">Seleccione la serie documental</div>
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