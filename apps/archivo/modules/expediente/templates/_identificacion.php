<?php use_helper('jQuery'); ?>

<script>
    function fn_datos_expediente(){
        serie = $('#select_serie').val();
//        $('#div_ubicacion_expediente').hide();

        if(serie!=''){
            $('#div_valores').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando clasificadores...');
            $('#cargando_ubicacion_id').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando estanteria...');
            $('#flash_error_id').hide();
            $('#div_ubicacion_expediente').hide();

            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_archivo_url'); ?>expediente/listarValores',
                type:'POST',
                dataType:'html',
                data: 's_id='+serie,

                success:function(data, textStatus){
                    $('#div_valores').html(data);
                }});

            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_archivo_url'); ?>expediente/listarUnidades',
                type:'POST',
                dataType:'html',
                data: 's_id='+serie,

                success:function(data, textStatus){
                    $('#div_unidad').html(data);
                }});
        } else {
            $('#div_valores').html('');
            $('#div_ubicacion_expediente').hide();
        }
    };
</script>

<div class="sf_admin_form_row sf_admin_text">

    <div>
        <label for="">Serie Documental</label>
        <div class="content">
            <select name="archivo_expediente[serie_documental_id]" id="select_serie" onchange="javascript: fn_datos_expediente();">
                <option value=""></option>
                <?php 
                    $series = Doctrine::getTable('Archivo_SerieDocumental')->seriesAutorizadasAFuncionario($sf_user->getAttribute('funcionario_id'),'archivar');

                    foreach ($series as $serie) { ?>
                        <option value="<?php echo $serie->getId(); ?>"><?php echo $serie->getNombre(); ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="help">Seleccione la serie documental.</div>
</div>

<div id="div_valores"></div>