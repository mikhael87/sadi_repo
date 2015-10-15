<?php
$variables_entorno = sfContext::getInstance()->getUser()->getAttribute('sf_variables_entorno');
$filtro = $sf_user->getAttribute('enviada.filters', array(), 'admin_module');

if(count($sf_user->getAttribute('enviada.filters', array(), 'admin_module')) != 0) {
    $ext= '_active';
    $status= 'Activo';
} else {
    $ext= '';
    $status= 'Inactivo';
}
?>
<script>
    function activar_bandeja_defecto(){
        $('#button_bandeja_defecto').hide();
        $('#cargando_bandeja_defecto').show();
        $('#notice_bandeja_defecto').fadeOut(8000);
        $.ajax(
        {
            url: '<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>enviada/setearBandejaDefecto',
            type:'POST',
            dataType:'html',
            data:'status_defecto='+$('#clon_status').val(),
            success:function(data, textStatus) {
                $('#cargando_bandeja_defecto').hide();
                $('#notice_bandeja_defecto').show();
                $('#notice_bandeja_defecto').fadeOut(5000);
            }
        });
    }
</script>

<style>
    .ui-dialog {
        top: 100px !important;
        box-shadow: #666 0.4em 0.5em 2em;
    }
</style>

<li>
<?php echo image_tag('icon/find24'.$ext, array('onclick' => '$(".sf_admin_filter").dialog("open")', 'style' => 'cursor:pointer; text-align: right; vertical-align: middle', 'title' => 'Filtrar Enviadas: <b>'.$status.'</b>', 'class' => 'tooltip')); ?>&nbsp;
<form style="display: inline;" id="quickfilter" method="post" action="<?php echo url_for('correspondencia_correspondencia_collection', array('action' => 'filter')) ?>">
    <input id="correspondencia_correspondencia_filters__csrf_token2" type="hidden" name="correspondencia_correspondencia_filters[_csrf_token]">
    <select class="tooltip" title="[!]Filtro rapido[/!]Seleccione el tipo de Documento que desea ver" id="clon" name="correspondencia_correspondencia_filters[formato]"></select>
    <select class="tooltip" title="[!]Filtro rapido[/!]Seleccione el Estatus de los Documentos que desea ver" id="clon_status" name="correspondencia_correspondencia_filters[status]"></select>
    <?php if(isset($filtro['status'])) { if($filtro['status'] != $variables_entorno['correspondencia']['bandeja_enviada_defecto']) { ?>
        <?php echo image_tag('icon/good_list.png', array('id' => 'button_bandeja_defecto', 'onclick' => 'activar_bandeja_defecto();', 'style' => 'cursor:pointer;', 'title' => 'Usar este filtro como bandeja de enviada por defecto.', 'class' => 'tooltip')); ?>
        <?php echo image_tag('icon/cargando.gif', array('id' => 'cargando_bandeja_defecto', 'size'=>'16x16', 'style' => 'display:none;')); ?>
        <font id="notice_bandeja_defecto" style="display: none;">Bandeja de enviada establecida por defecto.</font>
    <?php } } ?>
</form>
</li>

<div style="height: 10px;"></div>
<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>