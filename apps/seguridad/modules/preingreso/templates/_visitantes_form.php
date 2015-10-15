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
    
    function eliminar_cedula(id){
        $('#tr_cedula_lista_'+id).fadeOut(300, function(){ $(this).remove();});
    }    
</script>

<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label>Nº Cedula</label>
        <div class="content">
            <input type="text" name="persona_cedula" id="persona_cedula" onkeydown="return solo_num(event)" />
            <br/>
            <a href="#" onclick="agregar_cedula(); return false;">Agregar otra</a>
<!--            <input type="button" value="Buscar" name="Buscar" onclick="buscar_cedula(); return false;" />-->
            
            <br/><br/>
            <div>
                <table id="div_cedulas_listas" style="width: 100%;"></table>
            </div>
        </div>
    </div>
</div>