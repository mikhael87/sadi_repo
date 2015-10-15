<?php use_helper('jQuery'); ?>

<script>
    $(document).ready(function(){
        $('#div_fisico_button_new').click(function () {
            if($("#anexo_fisico_id").val()){
                $("#div_fisicos_listos").append('<tr id="tr_fisico_listo_'+$("#fisico_count").val()+'"><td><input name="anexos_fisicos[otros][]" id="check_fisico_listo_'+$("#fisico_count").val()+'" type="checkbox" onclick="javascript: fn_eliminar_fisico_listo('+$("#fisico_count").val()+'); return false;" value="'+$("#anexo_fisico_id").val()+'#'+jQuery.trim($("#anexo_fisico_observacion").val())+'" checked /></td><td>'+$("#anexo_fisico_id option:selected").text()+'</td><td>'+jQuery.trim($("#anexo_fisico_observacion").val())+'</td></tr>');
                count = Number($("#fisico_count").val()) + 1;
                $("#fisico_count").val(count);
                
                $("#anexo_fisico_id option[value='']").attr("selected", "selected");
                $("#anexo_fisico_observacion").val('');
            } else {
                alert('Debe seleccionar un tipo de anexo fisico.');
            }
        });
    });
    
    function fn_eliminar_fisico_listo(id){
        $('#tr_fisico_listo_'+id).fadeOut(300, function(){ $(this).remove();});
    }
    
    function fn_eliminar_fisico_edit(id){
        $("#fisico_delet").val($("#fisico_delet").val()+'#'+id);
        $('#tr_fisico_edit_'+id).fadeOut(300, function(){ $(this).remove();});
    }
    
    function toggle_div_adjuntar_fisicos(){
        $("#div_adjuntar_fisicos").toggle('slow');
    }        
</script>

        
<div class="sf_admin_form_row sf_admin_date sf_admin_form_field_fisicos">
    <div>
        <label for="correspondencia_fisicos_id">Tipo de fisico</label>
        <div class="content">
                <input id="fisico_count" name="adjunto_count" type="hidden" value="1"/>
                <input id="fisico_delet" name="anexos_fisicos[delet]" type="hidden" value="."/>
                <select name="anexos_fisicos[id]" id="anexo_fisico_id">
                    <option value=""></option>
                <?php 
                    $tipo_fisicos = Doctrine::getTable('Correspondencia_TipoAnexoFisico')
                        ->createQuery('a')
                        ->orderBy('nombre')->execute();

                    foreach ($tipo_fisicos as $fisico) {
                        echo '<option value="'.$fisico->getId().'">'.$fisico->getNombre()."</option>";
                    }
                ?>
                </select>
        </div>
    </div>
</div>    
<div class="sf_admin_form_row sf_admin_date sf_admin_form_field_fisicos">
    <div>
        <label for="correspondencia_fisicos_id">Observación</label>
        <div class="content">
            <textarea name="anexos_fisicos[observacion]" id="anexo_fisico_observacion" rows="2" cols="40"></textarea>
            <br/>
            <a href="#" onclick="return false;" id="div_fisico_button_new">Agregar otro</a>
            <br/><br/>
            <div>
                <table id="div_fisicos_listos">
                    <?php if(isset ($anexos_fisicos)) { foreach ($anexos_fisicos as $anexo_fisico) { ?>
                        <tr id="tr_fisico_edit_<?php echo $anexo_fisico->getId(); ?>">
                            <td><input id="check_fisico_edit_<?php echo $anexo_fisico->getId(); ?>" type="checkbox" onclick="javascript: fn_eliminar_fisico_edit(<?php echo $anexo_fisico->getId(); ?>); return false;" checked /></td>
                            <td><?php echo $anexo_fisico->gettafnombre(); ?></td>
                            <td><?php echo $anexo_fisico->getobservacion(); ?></td>
                        </tr>
                    <?php }} ?>
                </table>
            </div>
        </div>
    </div>
</div>    
