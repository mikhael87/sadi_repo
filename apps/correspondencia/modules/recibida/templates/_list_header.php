<style>
    .ui-dialog {
        top: 100px !important;
        box-shadow: #666 0.4em 0.5em 2em;
    }
</style>

<script>
    function responder_correspondencia(id){
        $('#content_window_right').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando tipos de documento...');

        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>recibida/responder',
            type:'POST',
            dataType:'html',
            data:'id='+id,
            success:function(data, textStatus){
                $('#content_window_right').html(data)
            }});
    }
</script>
<li>
<?php echo image_tag('icon/find24', array('onclick' => '$(".sf_admin_filter").dialog("open")', 'style' => 'cursor:pointer; text-align: right; vertical-align: middle', 'title' => 'Filtrar Recibidas')); ?>&nbsp;
<form style="display: inline;" id="quickfilter" method="post" action="<?php echo url_for('correspondencia_correspondencia_recibida_collection', array('action' => 'filter')) ?>">
          <input id="correspondencia_correspondencia_filters__csrf_token2" type="hidden" name="correspondencia_correspondencia_filters[_csrf_token]">
          <select class="tooltip" title="[!]Filtro rapido[/!]Seleccione el tipo de Documento que desea ver" id="clon" name="correspondencia_correspondencia_filters[formato]"></select>
          <select class="tooltip" title="[!]Filtro rapido[/!]Seleccione el Estatus de los Documentos que desea ver" id="clon_status_recepcion" name="correspondencia_correspondencia_filters[statusRecepcion]"></select>
</form>
</li>

<div style="height: 10px"></div>
<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>