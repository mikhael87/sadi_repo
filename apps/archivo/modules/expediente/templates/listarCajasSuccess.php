<script>
    $(document).ready(function(){
        var left_x = parseFloat(($("#div_select_cajas").css("width")).replace('px','')) + 5;
        $("#div_new_cajas").css("left",left_x);
    });
    
    function saveCaja()
    {
        if (confirm('Esta seguro de agregar una nueva caja a este estante.')) {

        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_archivo_url'); ?>expediente/saveCaja',
            type:'POST',
            dataType:'html',
            data:'datos[estante_id]='+$("#select_estante").val()+
                '&datos[tramo]='+$("#select_tramo").val()+
                '&datos[unidad_correlativo]='+$("#select_unidad_ubicacion").val(),
            success:function(data, textStatus){
                jQuery('#div_caja').html(data);
            }})
        }
    }
</script>

<div style="position: absolute;">
<div style="position: absolute;" id="div_select_cajas">
    <select name="archivo_expediente[caja_id]">
        <option value=""></option>
        <?php foreach ($cajas as $caja) { ?>
            <option value="<?php echo $caja->getId(); ?>"><?php echo $caja->getCorrelativo(); ?></option>
        <?php } ?>
    </select>
</div>
<div style="position: absolute; left: 0px; top: 3px; cursor: pointer;" id="div_new_cajas" onclick="javascript:saveCaja();"><?php echo image_tag('icon/new.png'); ?></div>
</div>