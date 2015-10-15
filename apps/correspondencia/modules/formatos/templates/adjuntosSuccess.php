<?php use_helper('jQuery'); ?>

<script>
    $(document).ready(function(){
        $('#div_adjunto_button_new').click(function () {
            if($("#correspondencia_adjunto_libre").val()){
                $("#correspondencia_adjunto_libre").hide();
                $("#div_adjunto_libre_listos").append('<input id="check_adjunto_libre_listo_'+$("#adjunto_count").val()+'" type="checkbox" onclick="javascript: fn_eliminar_adjunto_listo('+$("#adjunto_count").val()+'); return false;" checked /><x id="text_adjunto_libre_listo_'+$("#adjunto_count").val()+'"> '+$("#correspondencia_adjunto_libre").val()+'<br/></x>');
                $("#correspondencia_adjunto_libre").attr("id", "correspondencia_adjunto_libre_"+$("#adjunto_count").val());

                $("#div_adjunto_input").append('<input style="display: block;" type="file" id="correspondencia_adjunto_libre" name="correspondencia[adjunto][libre][]">');

                count = Number($("#adjunto_count").val()) + 1;
                $("#adjunto_count").val(count);
            } else {
                alert('Debe seleccionar un archivo.');
            }
        });
    });
    
    function fn_eliminar_adjunto_listo(id){
        $('#correspondencia_adjunto_libre_'+id).remove();
        $('#check_adjunto_libre_listo_'+id).fadeOut(300, function(){ $(this).remove();});
        $('#text_adjunto_libre_listo_'+id).fadeOut(300, function(){ $(this).remove();});
    }
    
    function fn_eliminar_adjunto_edit(id){
        $("#adjunto_delet").val($("#adjunto_delet").val()+'#'+id);
        $('#check_adjunto_libre_edit_'+id).fadeOut(300, function(){ $(this).remove();});
        $('#text_adjunto_libre_edit_'+id).fadeOut(300, function(){ $(this).remove();});
    }
    
    function toggle_div_adjuntar_archivo(){
        $("#div_adjuntar_archivo").toggle('slow');
    }    
</script>

<fieldset id="sf_fieldset_adjuntos">
    <h2><a href="#" onclick="toggle_div_adjuntar_archivo(); return false;">Adjuntar archivos</a></h2>
    
    <?php $display = 'none;'; if(isset ($adjuntos)) { $display = 'block;'; } ?>
    <div id="div_adjuntar_archivo" style="display: <?php echo $display; ?>">
        <div class="sf_admin_form_row sf_admin_text sf_admin_form_field_adjunto">
            <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'adjunto')) ?>

            <div>
                <label for="adjunto">Archivo</label>
                <div class="content" style="width: 650px;">
                    <input id="adjunto_count" name="adjunto_count" type="hidden" value="1"/>
                    <input id="adjunto_delet" name="correspondencia[adjunto][libre_delet]" type="hidden" value="."/>
                    <div id="div_adjunto_input"><input style="display: block;" type="file" id="correspondencia_adjunto_libre" name="correspondencia[adjunto][libre][]"></div>
                    <a href="#" onclick="return false;" id="div_adjunto_button_new">Agregar otro</a>
                    <br/><br/>
                    <div id="div_adjunto_libre_listos">
                    <?php if(isset ($adjuntos)) { foreach ($adjuntos as $adjunto) { ?>
                        <input id="check_adjunto_libre_edit_<?php echo $adjunto->getId(); ?>" type="checkbox" onclick="javascript: fn_eliminar_adjunto_edit(<?php echo $adjunto->getId(); ?>); return false;" checked />
                        <x id="text_adjunto_libre_edit_<?php echo $adjunto->getId(); ?>"> 
                            <?php echo $adjunto->getNombreOriginal(); ?><br/>
                        </x>
                    <?php }} ?>
                    </div>
                </div>
            </div>

            <div class="help"></div>
        </div>
    </div>
</fieldset>
