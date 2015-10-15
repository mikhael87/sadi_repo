<?php use_helper('jQuery'); ?>

<?php
if(count($sf_user->getAttribute('expedientes_compartidos.filters', array(), 'admin_module')) != 0) {
    $ext= '_active';
    $status= 'Activo';
} else {
    $ext= '';
    $status= 'Inactivo';
}
?>
<li>
<?php echo image_tag('icon/find24'.$ext, array('onclick' => '$(".sf_admin_filter").dialog("open")', 'style' => 'cursor:pointer; text-align: right; vertical-align: middle', 'title' => 'Filtrar Expedientes: <b>'.$status.'</b>', 'class' => 'tooltip')); ?>&nbsp;
<form style="display: inline;" id="quickfilter" method="post" action="<?php echo url_for('expediente/multipleBandeja') ?>">
          <select id="clon" name="bandeja_archivo">
              <option value="0" <?php echo (($sf_context->getModuleName()== 'expediente') ? 'selected' : ''); ?>>Mis expedientes</option>
              <option value="1" <?php echo (($sf_context->getModuleName()== 'prestamos_solicitados') ? 'selected' : ''); ?>>Expedientes que solicito</option>
              <option value="2" <?php echo (($sf_context->getModuleName()== 'expedientes_compartidos') ? 'selected' : ''); ?>>Expedientes compartidos</option>
          </select>
</form>
</li>


<?php
    $correlativos_activos = Doctrine_Query::create()
            ->select('cf.id, uc.id as unidad_correlativo, uc.unidad_id as unidad_id, uc.descripcion as descripcion_correlativo, cf.tipo_formato_id as tipo_formato_id, tf.nombre as tipo_formato')
            ->from('Correspondencia_CorrelativosFormatos cf')
            ->innerJoin('cf.Correspondencia_UnidadCorrelativo uc')
            ->innerJoin('cf.Correspondencia_TipoFormato tf')
            ->where('uc.unidad_id = ?',$sf_user->getAttribute('funcionario_unidad_id'))
            ->andWhere('tf.id = ?',13)
            ->andWhere('uc.tipo = ?','E')
            ->andWhere('uc.status = ?','A')
            ->orderBy('tf.nombre')
            ->execute();

            $formatos_legitimos = array();
            $i=0;

            foreach ($correlativos_activos as $correlativo_activo) {
                $formatos_legitimos[$i]=$correlativo_activo->getTipoFormatoId().'|'.
                                        $correlativo_activo->getUnidadId().'|'.
                                        $correlativo_activo->getUnidadCorrelativoId().'|'.
                                        $correlativo_activo->getId().'|'.
                                        $correlativo_activo->getDescripcionCorrelativo();

                $i++;
            }

        $sf_user->setAttribute('formatos_legitimos',$formatos_legitimos);
        $sf_user->setAttribute('call_module_master',sfConfig::get('sf_app_archivo_url').'expedientes_compartidos');
?>

<script>
    function solicitar_expedientes(expediente_id){

        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>formatos/librerias',
            type:'POST',
            dataType:'html',
            data:'forma=0&func=TraerDesdeArchivo&var[tipo_formato_id]=13&var[expediente_id]='+expediente_id,
            beforeSend: function(Obj){
                              $('#icono_carga').show();
                        },
            success:function(data, textStatus){
                $.ajax({
                    url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>formatos/formato',
                    type:'POST',
                    dataType:'html',
                    data:'tipo_formato_id=0',
                    success:function(data, textStatus){
                        jQuery('#div_expedientes_contenido').html(data);
                        $('#icono_carga').hide();
                }});
        }});

            <?php
            echo jq_remote_function(array('update' => 'div_expedientes_emisores',
            'url' => sfConfig::get('sf_app_correspondencia_url').'formatos/emisores',
            'with'=> "'tipo_formato_id=0'",))
            ?>

            <?php
            echo jq_remote_function(array('update' => 'div_expedientes_receptores',
            'url' => sfConfig::get('sf_app_correspondencia_url').'formatos/receptores',
            'with'=> "'tipo_formato_id=0'",))
            ?>

        $("#expedientes_div").slideDown();
    };

    function cerrar_solicitud(){
        $("#expedientes_div").slideUp();
        $("#div_expedientes_emisores").html('');
        $("#div_expedientes_receptores").html('');
        $("#div_expedientes_contenido").html('');
    };
</script>


<div style="position: absolute; right: 0px; top: 0px;">
    <div id="expedientes_div" style="padding: 4px; border-radius: 10px 10px 10px 10px; background-color: #000; z-index: 200000; position: fixed; right: 0px; top: 50px; width: 610px; height:430px; display: none;">
        <form id="form_enviada" method="post" action="<?php echo sfConfig::get('sf_app_correspondencia_url').'formatos/create'; ?>" enctype="multipart/form-data">

        <input type="hidden" name="correspondencia[formato][tipo_formato_id]" value="0">
        <input type="hidden" name="correspondencia[identificacion][prioridad]" value="S">
        <input type="hidden" name="correspondencia[identificacion][metodo_correlativo]" value="I">

        <div class="inner" style="border-radius: 8px 8px 8px 8px; background-color: #ebebeb; z-index: 200001; height:430px;">
            <div style="top: -15px; left: -15px; position: absolute;">
                <a href="#" onclick="javascript:cerrar_solicitud(); return false;"><?php echo image_tag('icon/icon_close.png'); ?></a>
            </div>
            <div id="expedientes_ver" style="overflow-y: auto; height:390px; width: 600px; top: 10px; left: 10px; position: absolute; overflow-x: hidden;">
                <?php echo image_tag('icon/cargando.gif', array('id'=>'icono_carga', 'style'=>'display: none; position: absolute; top: 48%; left: 48%')); ?>
                <div id="div_expedientes_emisores"></div>
                <div id="div_expedientes_receptores"></div>
                <div id="div_expedientes_contenido"></div>
            </div>
        </div>
        <div style="position: absolute; bottom: 10px; right: 30px;"><input type="submit" value="Enviar Solicitud"></div>
        </form>
    </div>
</div>