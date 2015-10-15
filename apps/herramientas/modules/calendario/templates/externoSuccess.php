<?php use_helper('jQuery'); ?>

<script>
    function agregar_cedula(){
        if($("#persona_cedula").val()){
            $("#div_cedulas_listas").append('<tr id="tr_cedula_lista_'+$("#persona_cedula").val()+'"><td style="width: 5px;"><input name="preingreso[cedulas][]" id="check_cedula_lista_'+$("#persona_cedula").val()+'" type="checkbox" onclick="eliminar_cedula('+$("#persona_cedula").val()+'); return false;" value="'+$("#persona_cedula").val()+'" checked /></td><td style="width: 30px;">'+$("#persona_cedula").val()+'</td><td><div id="busqueda_cedula_'+$("#persona_cedula").val()+'"></div></td></tr>');
            buscar_cedula($("#persona_cedula").val());
            $("#persona_cedula").val('');
        } else {
            alert('Debe escribir una cedula para poder agregar otra.');
        }
    }
        function buscar_cedula(cedula) {
        $('#busqueda_cedula_'+cedula).html('<?php echo image_tag('icon/cargando.gif', array('size'=>'20x20')); ?> Buscando en SAIME...');

        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: '<?php echo sfConfig::get('sf_app_seguridad_url'); ?>preingreso/visitanteForm',
            data: {cedula: cedula},
            success:function(data, textStatus){
                $('#busqueda_cedula_'+cedula).html(data);
            }
        })
        
    }
    function eliminar_cedula(id){
        $('#tr_cedula_lista_'+id).fadeOut(300, function(){ $(this).remove();});
    }    
</script>

<br/><br/>
<span style="font-size: 15px; font-weight: bolder;">Nº Cedula:</span>
<input type="text" name="persona_cedula" id="persona_cedula" onkeydown="return solo_num(event)" />
<br/>
<a href="#" onclick="agregar_cedula(); return false;">Agregar otra</a>
<!--            <input type="button" value="Buscar" name="Buscar" onclick="buscar_cedula(); return false;" />-->

<br/><br/>
<div>
    <table id="div_cedulas_listas" style="width: 100%;"></table>
</div>