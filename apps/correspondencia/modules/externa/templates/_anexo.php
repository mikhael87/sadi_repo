<?php use_helper('jQuery'); ?>

<script>
    var error_archivo_solo = 1;
//    SE COMENTA, VALIDACIONES JQUERY EN ASSETS
//    $(document).ready(function(){
//        $("form").submit(function() {
//            if($("#correspondencia_externa_adjunto").val()){
//                valida = fn_agregar_otro_externo();
//                if(valida == false){
//                    return false;
//                }
//            }
//
//            if($('#adjunto_count').val()==0){
//                alert('Debe seleccionar un archivo.');
//                return false;
//            }         
//            
//            if(!($('#select_organismo').val() && $('#select_persona').val() && $('#select_cargos').val())){
//                alert('Seleccione el emisor de la correspondencia (organismo, persona y cargo).');
//                return false;
//            }     
//        });
//    });
    
    function fn_agregar_otro_externo(){
        if($("#correspondencia_externa_adjunto").val()){
            //if( !qwe.match(/.(jpg)|(gif)|(png)|(bmp)|(pdf)$/) ){
            if($("#correspondencia_externa_adjunto").val().toLowerCase().match(/.(pdf)$/) ){
                //alert("wrong extension");   //actions like focus, not validate...
                $("#correspondencia_externa_adjunto").hide();
                $("#div_adjunto_listos").append('<input id="check_adjunto_listo_'+$("#adjunto_count").val()+'" type="checkbox" onclick="javascript: fn_eliminar_adjunto_listo('+$("#adjunto_count").val()+'); return false;" checked /><x id="text_adjunto_listo_'+$("#adjunto_count").val()+'"> '+$("#correspondencia_externa_adjunto").val()+'<br/></x>');
                $("#correspondencia_externa_adjunto").attr("id", "correspondencia_adjunto_"+$("#adjunto_count").val());

                $("#div_adjunto_input").append('<input style="display: block;" type="file" id="correspondencia_externa_adjunto" name="correspondencia[adjunto][nuevo][]">');

                count = Number($("#adjunto_count").val()) + 1;
                $("#adjunto_count").val(count);
                
                return true;
            }
            else {//right extension
                alert("Unicamente se admiten archivos en formato PDF"); //actions
                return false;
            }
        } else {
            alert('Debe seleccionar un archivo.');
            return false;
        }
    };
    
    function fn_eliminar_adjunto_listo(id){
        $('#correspondencia_externa_adjunto_'+id).remove();
        $('#check_adjunto_listo_'+id).fadeOut(300, function(){ $(this).remove();});
        $('#text_adjunto_listo_'+id).fadeOut(300, function(){ $(this).remove();});
    }
    
    function fn_eliminar_adjunto_edit(id){
        $("#adjunto_delet").val($("#adjunto_delet").val()+'#'+id);
        $('#check_adjunto_edit_'+id).fadeOut(300, function(){ $(this).remove();});
        $('#text_adjunto_edit_'+id).fadeOut(300, function(){ $(this).remove();});
    }
</script>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_adjunto">
    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'adjunto')) ?>

    <div>
        <label for="adjunto">Archivo</label>
        <div class="content" style="width: 650px;">
            <input id="adjunto_count" name="adjunto_count" type="hidden" value="0"/>
            <input id="adjunto_delet" name="correspondencia_externa[adjunto][delet]" type="hidden" value="."/>
            <div id="div_adjunto_input">
                <input style="display: block;" type="file" id="correspondencia_externa_adjunto" name="correspondencia[adjunto][nuevo][]">
                <input type="hidden" name="val_adjunto_externo" />
            </div>
            <a href="#" onclick="javascript:fn_agregar_otro_externo(); return false;" id="div_adjunto_button_new">Agregar otro</a>
            <br/><br/>
            <div id="div_adjunto_listos">
            <?php if(isset ($adjuntos)) { foreach ($adjuntos as $adjunto) { ?>
                <input id="check_adjunto_edit_<?php echo $adjunto->getId(); ?>" type="checkbox" onclick="javascript: fn_eliminar_adjunto_edit(<?php echo $adjunto->getId(); ?>); return false;" checked />
                <x id="text_adjunto_edit_<?php echo $adjunto->getId(); ?>"> 
                    <?php echo $adjunto->getNombreOriginal(); ?><br/>
                </x>
            <?php }} ?>
            </div>
        </div>
    </div>

    <div class="help">Unicamente podran ser adjuntados archivos en formato PDF</div>
</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_emisor_externo_resumen">
<?php if ($sf_user->hasFlash('error_resumen')): ?>
  <div class="error"><?php echo $sf_user->getFlash('error_resumen'); ?></div>
<?php endif; ?>
    <div>
        <label for="resumen_externo">Resumen</label>
        <div class="content">
            <textarea rows="6" cols="40" name="resumen_externo" id="resumen_externo"><?php echo $sf_user->getAttribute('resumen_externo'); ?></textarea>
        </div>
    </div>
</div>

<?php $sf_user->getAttributeHolder()->remove('resumen_externo'); ?>